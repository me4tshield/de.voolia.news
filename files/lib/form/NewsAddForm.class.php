<?php
namespace news\form;
use news\data\category\NewsCategory;
use news\data\category\NewsCategoryNodeTree;
use news\data\news\picture\NewsPicture;
use news\data\news\NewsAction;
use news\data\news\NewsEditor;
use news\data\news\NewsList;
use news\system\NEWSCore;
use wcf\data\category\Category;
use wcf\form\MessageForm;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\image\ImageHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\FileUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * Shows the form for new news entries.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsAddForm extends MessageForm {
	/**
	 * @see	\wcf\page\IPage::$action
	 */
	public $action = 'add';

	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.header.menu.news';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('user.news.canAddNews');

	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_CONTENT_NEWS');

	/**
	 * @see	\wcf\form\MessageForm::$attachmentObjectType
	 */
	public $attachmentObjectType = 'de.voolia.news.entry';

	/**
	 * list of category ids
	 * @var	array<integer>
	 */
	public $categoryIDs = array();

	/**
	 * category list
	 * @var	\RecursiveIteratorIterator
	 */
	public $categoryList = null;

	/**
	 * @see	\wcf\form\MessageForm::$enableMultilingualism
	 */
	public $enableMultilingualism = true;

	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;

	/**
	 * @see	\wcf\page\AbstractPage::$loginRequired
	 */
	public $loginRequired = true;

	/**
	 * news object
	 * @var	\news\data\news\News
	 */
	protected $news = null;

	/**
	 * enables a delayed publication
	 * @var	boolean
	 */
	public $enableDelayedPublication = 0;

	/**
	 * publication date (ISO 8601)
	 * @var	string
	 */
	public $publicationDate = '';

	/**
	 * enables automatic archivation
	 * @var	boolean
	 */
	public $enableAutomaticArchiving = 0;

	/**
	 * archiving date (ISO 8601)
	 * @var	string
	 */
	public $archivingDate = '';

	/**
	 * news picture id
	 * @var	integer
	 */
	public $pictureID = 0;

	/**
	 * picture object
	 * @var	\news\data\news\picture\NewsPicture
	 */
	public $picture = null;

	/**
	 * teaser
	 * @var	string
	 */
	public $teaser = '';

	/**
	 * source list
	 * @var	array<array<string>>
	 */
	public $sources = array();

	/**
	 * tags
	 * @var	array
	 */
	public $tags = array();

	/**
	 * news is hot
	 * @var	integer
	 */
	public $isHot = 0;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['categoryIDs']) && is_array($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);

		// quotes
		MessageQuoteManager::getInstance()->readParameters();

		// poll
		if ($this->canCreatePoll()) {
			PollManager::getInstance()->setObject('de.voolia.news.entry', 0);
		}
	}

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['enableDelayedPublication'])) $this->enableDelayedPublication = intval($_POST['enableDelayedPublication']);
		if (isset($_POST['publicationDate'])) $this->publicationDate = $_POST['publicationDate'];
		if (isset($_POST['enableAutomaticArchiving'])) $this->enableAutomaticArchiving = intval($_POST['enableAutomaticArchiving']);
		if (isset($_POST['archivingDate'])) $this->archivingDate = $_POST['archivingDate'];

		if (isset($_POST['teaser'])) $this->teaser = StringUtil::trim($_POST['teaser']);
		if (isset($_POST['tags']) && is_array($_POST['tags'])) $this->tags = ArrayUtil::trim($_POST['tags']);
		if (isset($_POST['isHot'])) $this->isHot = intval($_POST['isHot']);

		// news picture
		if (NEWS_ENABLE_NEWSPICTURE) {
			if (isset($_POST['pictureID'])) $this->pictureID = intval($_POST['pictureID']);
			$this->picture = new NewsPicture($this->pictureID);
		}

		// sources
		if (NEWS_ENTRY_ENABLE_SOURCES && isset($_POST['sourceLink']) && is_array($_POST['sourceLink']) && isset($_POST['sourceText']) && is_array($_POST['sourceText'])) {
			$sourceLinks = $_POST['sourceLink'];
			$sourceTexts = $_POST['sourceText'];

			foreach ($sourceLinks as $index => $sourceLink) {
				$this->sources[$index] = array(
					'sourceLink' => StringUtil::trim($sourceLink)
				);
				if (isset($sourceTexts[$index])) {
					$this->sources[$index]['sourceText'] = StringUtil::trim($sourceTexts[$index]);
					unset($sourceTexts[$index]);
				}
			}

			foreach ($sourceTexts as $index => $sourceText) {
				$this->sources[$index] = array(
					'sourceText' => StringUtil::trim($sourceText)
				);
			}
		}

		// quotes
		MessageQuoteManager::getInstance()->readFormParameters();

		// polls
		if ($this->canCreatePoll()) {
			PollManager::getInstance()->readFormParameters();
		}
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();

		// validate the news category ids
		if (empty($this->categoryIDs)) {
			throw new UserInputException('categoryIDs', 'empty');
		}
		foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			if ($category === null) throw new UserInputException('categoryIDs');

			// get category
			$category = new NewsCategory($category);

			// check, if the current user can use this category
			if (!$category->canUseCategory()) throw new UserInputException('categoryIDs');
		}

		// validate publication date
		if ($this->enableDelayedPublication) {
			$publicationDateTimestamp = @strtotime($this->publicationDate);
			if ($publicationDateTimestamp === false || $publicationDateTimestamp > 2147483647) {
				throw new UserInputException('publicationDate', 'invalid');
			}
		}

		// validate archiving date
		if ($this->enableAutomaticArchiving) {
			$archivingDateTimestamp = @strtotime($this->archivingDate);
			if ($archivingDateTimestamp === false || $archivingDateTimestamp <= TIME_NOW) {
				throw new UserInputException('archivingDate', 'invalid');
			}

			// date exceeds max integer
			if ($archivingDateTimestamp > 2147483647) {
				throw new UserInputException('archivingDate', 'invalid');
			}

			// archive news before publishing it at all
			if ($this->enableDelayedPublication && $publicationDateTimestamp >= $archivingDateTimestamp) {
				throw new UserInputException('archivingDate', 'beforePublication');
			}
		}

		// validate tags
		$this->validateTags();

		// validate source
		if (NEWS_ENTRY_ENABLE_SOURCES) {
			$this->validateSources();
		}

		// validate news picture
		if (NEWS_ENABLE_NEWSPICTURE) {
			if (NEWS_ENABLE_NEWSPICTURE_REQUIRED && !$this->pictureID) {
				throw new UserInputException('pictureID', 'empty');
			}
			if ($this->pictureID && !$this->picture->pictureID) {
				throw new UserInputException('pictureID', 'notValid');
			}
		}

		// validate poll
		if ($this->canCreatePoll()) {
			PollManager::getInstance()->validate();
		}
	}

	/**
	 * Validates the sources
	 */
	protected function validateSources() {
		foreach ($this->sources as $index => $source) {
			if (empty($source['sourceLink']) && empty($source['sourceText'])) {
				// ignore entry
				unset($this->sources[$index]);
				continue;
			}

			// add protocol if necessary
			if (!empty($source['sourceLink']) && !preg_match("/[a-z]:\/\//si", $source['sourceLink'])) $this->sources[$index]['sourceLink'] = 'http://'.$source['sourceLink'];
		}

		// check whether the user can/must add sources because of the category selection.
		$canAddSources = true;
		$canCreateNewsWithoutSources = true;
		foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			$category = new NewsCategory($category);

			// check permissions
			if (!$category->getPermission('canAddSources')) $canAddSources = false;
			if (!$category->getPermission('canCreateNewsWithoutSources')) $canCreateNewsWithoutSources = false;

			// we have everything we need :-)
			if (!$canAddSources && !$canCreateNewsWithoutSources) break;
		}

		if (empty($this->sources)) {
			if ($canAddSources && !$canCreateNewsWithoutSources) {
				throw new UserInputException('sources', 'empty');
			}
		} else {
			if (!$canAddSources) {
				// user can't add sources => delete them
				$this->sources = array();
			}

			// check whether there are too many sources given.
			if (NEWS_ENTRY_SOURCES_MAXCOUNT && count($this->sources) > NEWS_ENTRY_SOURCES_MAXCOUNT) {
				throw new UserInputException('sources', 'tooMany');
			}
		}
	}

	/**
	 * Validates the tags.
	 */
	protected function validateTags() {
		// check whether the user can/must set tags because of the category selection.
		$canSetTags = true;
		$canCreateNewsWithoutTags = true;
		foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			$category = new NewsCategory($category);

			// check permissions
			if (!$category->getPermission('canSetTags')) $canSetTags = false;
			if (!$category->getPermission('canCreateNewsWithoutTags')) $canCreateNewsWithoutTags = false;

			// we have everything we need :-)
			if (!$canSetTags && !$canCreateNewsWithoutTags) break;
		}

		if (empty($this->tags)) {
			if ($canSetTags && !$canCreateNewsWithoutTags) {
				throw new UserInputException('tags', 'empty');
			}
		} else if (!$canSetTags) {
			// user can't set tags => ignore them
			$this->tags = array();
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		$data = array(
			'subject' => $this->subject,
			'time'	=>  TIME_NOW,
			'text' => $this->text,
			'teaser' => $this->teaser,
			'userID' => (WCF::getUser()->userID ?: null),
			'username' => (WCF::getUser()->userID ? WCF::getUser()->username : $this->username),
			'languageID' => $this->languageID,
			'enableBBCodes' => $this->enableBBCodes,
			'enableHtml' => $this->enableHtml,
			'enableSmilies' => $this->enableSmilies,
			'isActive' => 1,
			'isDeleted' => 0,
			'isHot'	=> $this->isHot,
			'views'	=> 0
		);

		// delayed publication
		if ($this->enableDelayedPublication) {
			$data['isPublished'] = 0;
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $this->publicationDate, WCF::getUser()->getTimeZone());
			$data['publicationDate'] = $dateTime->getTimestamp();
		}

		// automatic archivation
		if ($this->enableAutomaticArchiving) {
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $this->archivingDate, WCF::getUser()->getTimeZone());
			$data['archivingDate'] = $dateTime->getTimestamp();
		}

		// news picture
		if (NEWS_ENABLE_NEWSPICTURE && $this->pictureID) {
			$data['pictureID'] = $this->pictureID;
		}

		$newsData = array(
			'attachmentHandler' => $this->attachmentHandler,
			'categoryIDs' => $this->categoryIDs,
			'data' => $data
		);

		if (NEWS_ENTRY_ENABLE_SOURCES) {
			$newsData['sources'] = $this->sources;
		}
		if (MODULE_TAGGING) {
			$newsData['tags'] = $this->tags;
		}

		$this->objectAction = new NewsAction(array(), 'create', $newsData);
		$resultvalues = $this->objectAction->executeAction();

		$this->news = $resultvalues['returnValues'];

		// quotes
		MessageQuoteManager::getInstance()->saved();

		// polls
		if ($this->canCreatePoll()) {
			$pollID = PollManager::getInstance()->save($this->news->newsID);
			if ($pollID) {
				$newsEditor = new NewsEditor($this->news);
				$newsEditor->update(array(
					'pollID' => $pollID
				));
			}
		}

		$this->saved();

		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array('application' => 'news','object' => $this->news)));

		exit;
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// get the accessible news categories
		$categoryTree = new NewsCategoryNodeTree('de.voolia.news.category');
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);

		if (empty($_POST)) {
			// multilingualism
			if (!empty($this->availableContentLanguages)) {
				if (!$this->languageID) {
					$language = LanguageFactory::getInstance()->getUserLanguage();
					$this->languageID = $language->languageID;
				}

				if (!isset($this->availableContentLanguages[$this->languageID])) {
					$languageIDs = array_keys($this->availableContentLanguages);
					$this->languageID = array_shift($languageIDs);
				}
			}

			// set default publication and archivation date
			$dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
			$dateTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->publicationDate = $this->archivingDate = $dateTime->format('c');
		}

		// add breadcrumbs
		NEWSCore::getInstance()->setBreadcrumbs();
	}

	/**
	 * Returns true, if author can create a new poll.
	 * 
	 * @return	boolean
	 */
	protected function canCreatePoll() {
		if (!MODULE_POLL || !WCF::getSession()->getPermission('user.news.canCreatePoll')) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		// quotes
		MessageQuoteManager::getInstance()->assignVariables();

		// poll
		if ($this->canCreatePoll()) {
			PollManager::getInstance()->assignVariables();
		}

		WCF::getTPL()->assign(array(
			'action' => $this->action,
			'subject' => $this->subject,
			'text' => $this->text,
			'teaser' => $this->teaser,
			'categoryList' => $this->categoryList,
			'categoryIDs' => $this->categoryIDs,
			'enableAutomaticArchiving' => $this->enableAutomaticArchiving,
			'archivingDate' => $this->archivingDate,
			'enableDelayedPublication' => $this->enableDelayedPublication,
			'publicationDate' => $this->publicationDate,
			'pictureID' => $this->pictureID,
			'picture' => $this->picture,
			'isHot' => $this->isHot,
			'sources' => $this->sources,
			'tags' => $this->tags,
			'showSignatureSetting' => false,
			'messageQuoteCount' => MessageQuoteManager::getInstance()->countQuotes()
		));
	}
}
