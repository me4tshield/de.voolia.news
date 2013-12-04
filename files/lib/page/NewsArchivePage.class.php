<?php
namespace news\page;
use news\data\category\NewsCategory;
use news\data\category\NewsCategoryNodeTree;
use news \data\news\NewsCategoryList;
use news\system\cache\builder\NewsStatsCacheBuilder;
use news\system\NEWSCore;
use wcf\data\category\Category;
use wcf\data\user\online\UsersOnlineList;
use wcf\page\SortablePage;
use wcf\system\category\CategoryHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

/**
 * Shows the news archive page.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsArchivePage extends SortablePage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.header.menu.news';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('user.news.canViewNews');

	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'news\data\news\AccessibleNewsList';

	/**
	 * @see	\wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = 'time';

	/**
	 * @see	\wcf\page\SortablePage::$defaultSortOrder
	 */
	public $defaultSortOrder = 'DESC';

	/**
	 * @see	\wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array('newsID', 'subject', 'categoryID', 'time');

	/**
	 * list filter
	 * @var	string
	 */
	public $filter = '';

	/**
	 * @see	\wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = NEWS_ITEMS_PER_PAGE;

	/**
	 * letters
	 * @var	string
	 */
	public $letter = '';

	/**
	 * category id
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * category
	 * @var	\wcf\data\category\Category
	 */
	public $category = null;

	/**
	 * available categories
	 * @var	array<\wcf\data\category\Category>
	 */
	public $categoryList = null;
	public $objectTypeName = 'de.voolia.news.category';

	/**
	 * available letters
	 * @var	string
	 */
	public static $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;

	/**
	 * users online list
	 * @var	\wcf\data\user\online\UsersOnlineList
	 */
	public $usersOnlineList = null;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// letter
		if (isset($_REQUEST['letter']) && mb_strlen($_REQUEST['letter']) == 1 && mb_strpos(self::$availableLetters, $_REQUEST['letter']) !== false) {
			$this->letter = $_REQUEST['letter'];
		}

		// news by category
		if (isset($_REQUEST['id'])) {
			// get category id
			$this->categoryID = intval($_REQUEST['id']);

			// get category by id
			$this->category = CategoryHandler::getInstance()->getCategory($this->categoryID);

			// check category
			if ($this->category === null) {
				throw new IllegalLinkException();
			}

			$this->category = new NewsCategory($this->category);

			if (!$this->category->isAccessible()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// categories
		$categoryTree = new NewsCategoryNodeTree($this->objectTypeName);
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);

		// stats
		$this->stats = NewsStatsCacheBuilder::getInstance()->getData();

		$this->stats['categories'] = count(CategoryHandler::getInstance()->getCategories($this->objectTypeName));
		$this->stats['comments'] = count(CommentHandler::getInstance()->getObjectType('de.voolia.news.comment'));

		// users online list
		if (MODULE_USERS_ONLINE && NEWS_INDEX_ENABLE_USERS_ONLINE_LIST) {
			$this->usersOnlineList = new UsersOnlineList();
			$this->usersOnlineList->readStats();
			$this->usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
			$this->usersOnlineList->readObjects();
		}

		// add breadcrumbs
		NEWSCore::getInstance()->setBreadcrumbs(($this->category !== null ? $this->category->getParentCategories() : array()));
	}

	/**
	 * @see	\wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		if ($this->categoryID) {
			$this->objectList = new NewsCategoryList(array($this->categoryID));
		} else {
			parent::initObjectList();
		}

		// letter filter
		if (!empty($this->letter)) {
			if ($this->letter == '#') {
				$this->objectList->getConditionBuilder()->add("SUBSTRING(news.subject,1,1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
			} else {
				$this->objectList->getConditionBuilder()->add("news.subject LIKE ?", array($this->letter.'%'));
			}
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		// configuration for dashboard boxes
		DashboardHandler::getInstance()->loadBoxes('de.voolia.news.NewsArchivePage', $this);

		WCF::getTPL()->assign(array(
			'filter' => $this->filter,
			'letters' => str_split(self::$availableLetters),
			'letter' => $this->letter,
			'stats' => $this->stats,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.voolia.news.NewsArchivePage'),
			'sidebarName' => 'de.voolia.news.NewsArchivePage',
			'usersOnlineList' => $this->usersOnlineList,
			'allowSpidersToIndexThisPage' => true
		));
	}
}
