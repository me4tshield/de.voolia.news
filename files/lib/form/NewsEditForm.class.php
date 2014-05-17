<?php
namespace news\form;
use news\data\news\picture\NewsPicture;
use news\data\news\News;
use news\data\news\NewsAction;
use news\data\news\NewsEditor;
use wcf\form\MessageForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\tagging\TagEngine;
use wcf\system\visitTracker\VisitTracker;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\system\WCF;

/**
 * Shows the edit form for news entries.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsEditForm extends NewsAddForm {
	/**
	 * @see	\wcf\page\IPage::$action
	 */
	public $action = 'edit';

	/**
	 * news id
	 * @var	integer
	 */
	public $newsID = 0;

	/**
	 * news object
	 * @var	\wcf\data\news\News
	 */
	public $news = null;

	/**
	 * news edit reason
	 * @var	string
	 */
	public $editReason = '';

	/**
	 * news edit reason
	 * @var	integer
	 */
	public $editNoteSuppress = 0;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get the news by id
		if (isset($_REQUEST['id'])) $this->newsID = intval($_REQUEST['id']);
		$this->news = new News($this->newsID);
		if (!$this->news->newsID) {
			throw new IllegalLinkException();
		}

		// check news permissions
		if (!$this->news->isEditable()) {
			throw new PermissionDeniedException();
		}

		// set attachment object id
		$this->attachmentObjectID = $this->news->newsID;

		// polls
		if ($this->canCreatePoll()) {
			PollManager::getInstance()->setObject('de.voolia.news.entry', $this->news->newsID, $this->news->pollID);
		}
	}

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		// edit note
		if (isset($_POST['editReason'])) $this->editReason = StringUtil::trim($_POST['editReason']);
		if (isset($_POST['editNoteSuppress'])) $this->editNoteSuppress = intval($_POST['editNoteSuppress']);
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		if (!WCF::getSession()->getPermission('mod.news.canEditNewsWithoutNote') && empty($this->editReason)) {
			throw new UserInputException('editReason', 'empty');
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		MessageForm::save();

		if (WCF::getSession()->getPermission('mod.news.canEditNewsWithoutNote') && $this->editNoteSuppress == 1) {
			$this->news->editCount = 0;
			$this->editReason = '';
			$this->news->editTime = 0;
		}

		// save the news entry
		$data = array(
			'subject' => $this->subject,
			'text' => $this->text,
			'teaser' => $this->teaser,
			'languageID' => $this->languageID,
			'enableBBCodes' => $this->enableBBCodes,
			'enableHtml' => $this->enableHtml,
			'enableSmilies' => $this->enableSmilies,
			'editCount' => $this->news->editCount + 1,
			'editReason' => $this->editReason,
			'editTime' => TIME_NOW,
			'editUser' => WCF::getUser()->username,
			'editReason' => $this->editReason,
			'editNoteSuppress' => $this->editNoteSuppress,
			'isHot' => $this->isHot,
			'location' => $this->locationData,
			'longitude' => $this->longitude,
			'latitude' => $this->latitude
		);

		// delayed publication
		if ($this->enableDelayedPublication) {
			$data['isPublished'] = 0;
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $this->publicationDate, WCF::getUser()->getTimeZone());
			$data['publicationDate'] = $dateTime->getTimestamp();
		} else {
			$data['isPublished'] = 1;
			$data['publicationDate'] = 0;
		}

		// automatic archivation
		if ($this->enableAutomaticArchiving) {
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $this->archivingDate, WCF::getUser()->getTimeZone());
			$data['archivingDate'] = $dateTime->getTimestamp();
		} else {
			$data['archivingDate'] = 0;
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

		// save poll
		if ($this->canCreatePoll()) {
			$pollID = PollManager::getInstance()->save($this->news->newsID);
			if ($pollID) {
				$newsEditor = new NewsEditor($this->news);
				$newsEditor->update(array(
					'pollID' => $pollID
				));
			}
		}

		$this->objectAction = new NewsAction(array($this->news), 'update', $newsData);
		$this->objectAction->executeAction();

		$this->saved();

		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'object' => $this->news
		)));

		exit;
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			$this->subject = $this->news->subject;
			$this->text = $this->news->text;
			$this->teaser = $this->news->teaser;
			$this->languageID = $this->news->languageID;
			$this->pictureID = $this->news->pictureID;
			$this->tags = $this->news->tags;
			$this->enableBBCodes = $this->news->enableBBCodes;
			$this->enableHtml = $this->news->enableHtml;
			$this->enableSmilies = $this->news->enableSmilies;
			$this->editReason = $this->news->editReason;
			$this->editNoteSuppress = $this->news->editNoteSuppress;
			$this->isHot = $this->news->isHot;
			if ($this->news->location) $this->enableLocation = 1;
			$this->locationData = $this->news->location;
			$this->longitude = $this->news->longitude;
			$this->latitude = $this->news->latitude;

			if (!$this->news->isPublished) {
				$this->enableDelayedPublication = 1;
				$dateTime = DateUtil::getDateTimeByTimestamp($this->news->publicationDate);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->publicationDate = $dateTime->format('c');
			}

			if ($this->news->archivingDate) {
				$this->enableAutomaticArchiving = 1;
				$dateTime = DateUtil::getDateTimeByTimestamp($this->news->archivingDate);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->archivingDate = $dateTime->format('c');
			}

			foreach ($this->news->getCategories() as $category) {
				$this->categoryIDs[] = $category->categoryID;
			}

			// tagging
			if (MODULE_TAGGING) {
				$tags = TagEngine::getInstance()->getObjectTags('de.voolia.news.entry', $this->news->newsID, array($this->news->languageID));
				foreach ($tags as $tag) {
					$this->tags[] = $tag->name;
				}
			}

			// load sources
			if (NEWS_ENTRY_ENABLE_SOURCES) {
				foreach ($this->news->getSources() as $source) {
					$this->sources[] = array(
						'sourceText' => $source->sourceText,
						'sourceLink' => $source->sourceLink
					);
				}
			}
		}

		// get news picture
		if ($this->pictureID) {
			$this->picture = new NewsPicture($this->pictureID);
		}

		// add breadcrumbs
		WCF::getBreadcrumbs()->add($this->news->getBreadcrumb());
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'newsID' => $this->newsID,
			'news' => $this->news,
			'editReason' => $this->editReason,
			'editNoteSuppress' => $this->editNoteSuppress
		));
	}
}
