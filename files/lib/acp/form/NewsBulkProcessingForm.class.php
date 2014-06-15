<?php
namespace news\acp\form;
use news\data\category\NewsCategoryNodeTree;
use news\data\news\NewsAction;
use news\data\news\NewsList;
use wcf\form\AbstractForm;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;
use wcf\system\WCF;

/**
 * Shows the News bulk processing form.
 * 
 * @author	Udo Zaydowicz
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsBulkProcessingForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.newsBulkProcessing';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.news.canManageCategory');
	
	/**
	 * list of available actions
	 * @var	array<string>
	 */
	public $availableActions = array('setAsHot', 'unsetAsHot', 'activateComments', 'deactivateComments', 'archive', 'trash', 'delete', 'restore', 'disable', 'enable', 'publish', 'changeLanguage', 'move');
	
	/**
	 * number of affected news
	 * @var	integer
	 */
	public $affectedNews = 0;
	
	/**
	 * time conditions
	 * @var	string
	 */
	public $timeFrom = '';
	public $timeTo = '';
	
	/**
	 * names of news authors
	 * @var	string
	 */
	public $authors = '';
	
	/**
	 * poll state
	 * @var	boolean
	 */
	public $isPoll = 0;
	public $isNotPoll = 0;
	
	/**
	 * active state
	 * @var	boolean
	 */
	public $isActive = 0;
	public $isNotActive = 0;
	
	/**
	 * deleted state
	 * @var	boolean
	 */
	public $isDeleted = 0;
	public $isNotDeleted = 0;
	
	/**
	 * published state
	 * @var	boolean
	 */
	public $isPublished = 0;
	public $isNotPublished = 0;
	
	/**
	 * archived state
	 * @var	boolean
	 */
	public $isArchived = 0;
	public $isNotArchived = 0;
	
	/**
	 * hot state
	 * @var	boolean
	 */
	public $isHot = 0;
	public $isNotHot = 0;
	
	/**
	 * announcement state
	 * @var	boolean
	 */
	public $isAnnouncement = 0;
	public $isNotAnnouncement = 0;
	
	/**
	 * commentable state
	 * @var	boolean
	 */
	public $isCommentable = 0;
	public $isNotCommentable = 0;
	
	/**
	 * comments state
	 * @var	boolean
	 */
	public $hasComments = 0;
	public $hasNoComments = 0;
	
	/**
	 * category state
	 * @var	boolean
	 */
	public $hasCategory = 0;
	public $hasNoCategory = 0;
	
	/**
	 * news list object
	 * @var	\news\data\news\NewsList
	 */
	public $newsList = null;
	
	/**
	 * selected news category ids
	 * @var	array<integer>
	 */
	public $categoryIDs = array();
	
	/**
	 * category id to move news to
	 * @var	array<integer>
	 */
	public $moveCategoryID = 0;
	
	/**
	 * category list
	 * @var	\RecursiveIteratorIterator
	 */
	public $categoryList = null;
	
	/**
	 *  language ids
	 * @var	array<integer>
	 */
	public $languageIDs = array();
	
	/**
	 * id of the new news language
	 * @var	integer
	 */
	public $newLanguageID = 0;
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['timeFrom'])) $this->timeFrom = $_POST['timeFrom'];
		if (isset($_POST['timeTo'])) $this->timeTo = $_POST['timeTo'];
		if (isset($_POST['authors'])) $this->authors = StringUtil::trim($_POST['authors']);
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_POST['categoryIDs']);
		
		if (isset($_POST['isPoll'])) $this->isPoll = intval($_POST['isPoll']);
		if (isset($_POST['isNotPoll'])) $this->isNotPoll = intval($_POST['isNotPoll']);
		if (isset($_POST['isActive'])) $this->isActive = intval($_POST['isActive']);
		if (isset($_POST['isNotActive'])) $this->isNotActive = intval($_POST['isNotActive']);
		if (isset($_POST['isDeleted'])) $this->isDeleted = intval($_POST['isDeleted']);
		if (isset($_POST['isNotDeleted'])) $this->isNotDeleted = intval($_POST['isNotDeleted']);
		if (isset($_POST['isPublished'])) $this->isPublished = intval($_POST['isPublished']);
		if (isset($_POST['isNotPublished'])) $this->isNotPublished = intval($_POST['isNotPublished']);
		if (isset($_POST['isArchived'])) $this->isArchived = intval($_POST['isArchived']);
		if (isset($_POST['isNotArchived'])) $this->isNotArchived = intval($_POST['isNotArchived']);
		if (isset($_POST['isHot'])) $this->isHot = intval($_POST['isHot']);
		if (isset($_POST['isNotHot'])) $this->isNotHot = intval($_POST['isNotHot']);
		if (isset($_POST['isAnnouncement'])) $this->isAnnouncement = intval($_POST['isAnnouncement']);
		if (isset($_POST['isNotAnnouncement'])) $this->isNotAnnouncement = intval($_POST['isNotAnnouncement']);
		if (isset($_POST['isCommentable'])) $this->isCommentable = intval($_POST['isCommentable']);
		if (isset($_POST['isNotCommentable'])) $this->isNotCommentable = intval($_POST['isNotCommentable']);
		if (isset($_POST['hasComments'])) $this->hasComments = intval($_POST['hasComments']);
		if (isset($_POST['hasNoComments'])) $this->hasNoComments = intval($_POST['hasNoComments']);
		if (isset($_POST['hasCategory'])) $this->hasCategory = intval($_POST['hasCategory']);
		if (isset($_POST['hasNoCategory'])) $this->hasNoCategory = intval($_POST['hasNoCategory']);
		if (isset($_POST['languageIDs']) && is_array($_POST['languageIDs'])) $this->languageIDs = ArrayUtil::toIntegerArray($_POST['languageIDs']);
		if (isset($_POST['newLanguageID'])) $this->newLanguageID = intval($_POST['newLanguageID']);
		if (isset($_POST['moveCategoryID'])) $this->moveCategoryID = intval($_POST['moveCategoryID']);
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// action
		if (!in_array($this->action, $this->availableActions)) {
			throw new UserInputException('action');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		$this->newsList = new NewsList();
		
		parent::save();
		
		// time from / to
		$timeFrom = @strtotime($this->timeFrom);
		$timeTo = @strtotime($this->timeTo);
		if (!empty($this->timeFrom) && $timeFrom) $this->newsList->getConditionBuilder()->add('time > ?', array($timeFrom));
		if (!empty($this->timeTo) && $timeTo) $this->newsList->getConditionBuilder()->add('time < ?', array($timeTo));
		
		// authors
		if (!empty($this->authors)) {
			$authors = preg_split('/\s*,\s*/', $this->authors, -1, PREG_SPLIT_NO_EMPTY);
			$this->newsList->getConditionBuilder()->add('username IN (?)', array($authors));
		}
		
		// category ids
		if (!empty($this->categoryIDs)) {
			$this->newsList->getConditionBuilder()->add('newsID IN (SELECT newsID FROM news'.WCF_N.'_news_to_category WHERE categoryID IN (?))', array($this->categoryIDs));
		}
		
		// languageIDs
		if (!empty($this->languageIDs)) {
			$zero = array_search(0, $this->languageIDs);
			if ($zero !== false) {
				if (count($this->languageIDs) == 1) {
					$this->newsList->getConditionBuilder()->add("languageID IS NULL");
				}
				else {
					$this->newsList->getConditionBuilder()->add("(languageID IN (?) OR languageID IS NULL)", array($this->languageIDs));
				}
			}
			else {
				$this->newsList->getConditionBuilder()->add("languageID IN (?)", array($this->languageIDs));
			}
		}
		
		// stati
		if ($this->isPoll) $this->newsList->getConditionBuilder()->add("pollID IS NOT NULL");
		if ($this->isNotPoll) $this->newsList->getConditionBuilder()->add("pollID IS NULL");
		if ($this->isActive || $this->action == 'disable') $this->newsList->getConditionBuilder()->add("isActive = ?", array(1));
		if ($this->isNotActive || $this->action == 'enable') $this->newsList->getConditionBuilder()->add("isActive = ?", array(0));
		if ($this->isDeleted || $this->action == 'restore') $this->newsList->getConditionBuilder()->add("isDeleted = ?", array(1));
		if ($this->isNotDeleted || $this->action == 'trash') $this->newsList->getConditionBuilder()->add("isDeleted = ?", array(0));
		if ($this->isPublished) $this->newsList->getConditionBuilder()->add("isPublished = ?", array(1));
		if ($this->isNotPublished || $this->action == 'publish') $this->newsList->getConditionBuilder()->add("isPublished = ?", array(0));
		if ($this->isArchived) $this->newsList->getConditionBuilder()->add("isArchived = ?", array(1));
		if ($this->isNotArchived || $this->action == 'archive') $this->newsList->getConditionBuilder()->add("isArchived = ?", array(0));
		if ($this->isHot || $this->action == 'unsetAsHot') $this->newsList->getConditionBuilder()->add("isHot = ?", array(1));
		if ($this->isNotHot || $this->action == 'setAsHot') $this->newsList->getConditionBuilder()->add("isHot = ?", array(0));
		if ($this->isAnnouncement) $this->newsList->getConditionBuilder()->add("isAnnouncement = ?", array(1));
		if ($this->isNotAnnouncement) $this->newsList->getConditionBuilder()->add("isAnnouncement = ?", array(0));
		if ($this->isCommentable || $this->action == 'deactivateComments') $this->newsList->getConditionBuilder()->add("isCommentable = ?", array(1));
		if ($this->isNotCommentable || $this->action == 'activateComments') $this->newsList->getConditionBuilder()->add("isCommentable = ?", array(0));
		if ($this->hasComments) $this->newsList->getConditionBuilder()->add("comments > ?", array(0));
		if ($this->hasNoComments) $this->newsList->getConditionBuilder()->add("comments = ?", array(0));
		if ($this->hasCategory) $this->newsList->getConditionBuilder()->add('newsID IN (SELECT DISTINCT newsID FROM news'.WCF_N.'_news_to_category)');
		if ($this->hasNoCategory) $this->newsList->getConditionBuilder()->add('newsID NOT IN (SELECT DISTINCT newsID FROM news'.WCF_N.'_news_to_category)');
		
		$this->newsList->readObjects();
		
		// execute if news count > 0
		if (count($this->newsList)) {
			
			if ($this->action == 'changeLanguage') {
				$parameters['data'] = array(
					'languageID' => $this->newLanguageID ?: null
				);
				$action = new NewsAction($this->newsList->getObjects(), 'update', $parameters);
				$action->executeAction();
			}
			
			else if ($this->action == 'move') {
				$ids = array();
				foreach ($this->newsList->getObjects() as $news) {
					$ids[] = $news->newsID;
				}
				
				// remove from all categories
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('newsID IN (?)', array($ids));
				$sql = "DELETE FROM	news".WCF_N."_news_to_category
						".$conditionBuilder;
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				
				// insert new
				if ($this->moveCategoryID) {
					$sql = "INSERT INTO	news".WCF_N."_news_to_category (categoryID, newsID)
							VALUES (?, ?)";
					$statement = WCF::getDB()->prepareStatement($sql);
					foreach ($ids as $id) {
						$statement->execute(array($this->moveCategoryID, $id));
					}
				}	
			}

			else {
				$action = new NewsAction($this->newsList->getObjects(), $this->action);
				$action->executeAction();
			}
		}
		
		$this->affectedNews = count($this->newsList);
		
		$this->saved();
		
		WCF::getTPL()->assign('affectedNews', $this->affectedNews);
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// get the accessible news categories
		$categoryTree = new NewsCategoryNodeTree('de.voolia.news.category');
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);
		
		// get languages
		$languages = array();
		if (LanguageFactory::getInstance()->multilingualismEnabled()) {
			$languages = LanguageFactory::getInstance()->getContentLanguages();
		}
		
		WCF::getTPL()->assign(array(
			'timeFrom' => $this->timeFrom,
			'timeTo' => $this->timeTo,
			'authors' => $this->authors,
			'isPoll' => $this->isPoll,
			'isNotPoll' => $this->isNotPoll,
			'isActive' => $this->isActive,
			'isNotActive' => $this->isNotActive,
			'isDeleted' => $this->isDeleted,
			'isNotDeleted' => $this->isNotDeleted,
			'isPublished' => $this->isPublished,
			'isNotPublished' => $this->isNotPublished,
			'isArchived' => $this->isArchived,
			'isNotArchived' => $this->isNotArchived,
			'isHot' => $this->isHot,
			'isNotHot' => $this->isNotHot,
			'isAnnouncement' => $this->isAnnouncement,
			'isNotAnnouncement' => $this->isNotAnnouncement,
			'isCommentable' => $this->isCommentable,
			'isNotCommentable' => $this->isNotCommentable,
			'hasComments' => $this->hasComments,
			'hasNoComments' => $this->hasNoComments,
			'hasCategory' => $this->hasCategory,
			'hasNoCategory' => $this->hasNoCategory,
			'categoryList' => $this->categoryList,
			'categoryIDs' => $this->categoryIDs,
			'languages' => $languages,
			'languageIDs' => $this->languageIDs,
			'newLanguageID' => $this->newLanguageID,
			'moveCategoryID' => $this->moveCategoryID
		));
	}
}
