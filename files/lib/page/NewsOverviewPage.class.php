<?php
namespace news\page;
use news\data\category\NewsCategory;
use news\data\news\NewsCategoryList;
use news\system\cache\builder\NewsStatsCacheBuilder;
use news\system\NEWSCore;
use wcf\data\category\Category;
use wcf\data\user\online\UsersOnlineList;
use wcf\data\user\User;
use wcf\page\MultipleLinkPage;
use wcf\system\category\CategoryHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;

/**
 * Shows the news overview page.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsOverviewPage extends MultipleLinkPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.header.menu.news';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('user.news.canViewNews');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_CONTENT_NEWS');

	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'news\data\news\AccessibleNewsList';

	/**
	 * @see	\wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = NEWS_ITEMS_PER_PAGE;

	/**
	 * @see	\wcf\page\MultipleLinkPage::$sortField
	 */
	public $sortField = 'time';

	/**
	 * @see	\wcf\page\MultipleLinkPage::$sortOrder
	 */
	public $sortOrder = 'DESC';

	/**
	 * letter
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
	 * available letters
	 * @var	string
	 */
	public static $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * news statistics
	 * @var	array
	 */
	public $statistics = array ();

	/**
	 * users online list
	 * @var	\wcf\data\user\online\UsersOnlineList
	 */
	public $usersOnlineList = null;

	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;

	/**
	 * user id
	 * @var	integer
	 */
	public $userID = 0;

	/**
	 * user object
	 * @var	\wcf\data\user\User
	 */
	public $user = null;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// letters
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

		// news by userID
		else if (isset($_REQUEST['userID'])) {
			$this->userID = intval($_REQUEST['userID']);
			$this->user = new User($this->userID);
			if (!$this->user->userID) {
				throw new IllegalLinkException();
			}
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// news stats
		$this->stats = NewsStatsCacheBuilder::getInstance()->getData();
		$this->stats['categories'] = count(CategoryHandler::getInstance()->getCategories('de.voolia.news.category'));

		// users online list
		if (MODULE_USERS_ONLINE && NEWS_INDEX_ENABLE_USERS_ONLINE_LIST) {
			$this->usersOnlineList = new UsersOnlineList();
			$this->usersOnlineList->readStats();
			$this->usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
			$this->usersOnlineList->readObjects();
		}

		// add breadcrumbs
		if ($this->category !== null) {
			NEWSCore::getInstance()->setBreadcrumbs($this->category->getParentCategories());
		} else if (PageMenu::getInstance()->getLandingPage()->menuItem == 'news.header.menu.news') {
			// remove default breadcrumb entry and set current page as 'website'
			WCF::getBreadcrumbs()->remove(0);

			// meta tags
			MetaTagHandler::getInstance()->addTag('og:url', 'og:url', LinkHandler::getInstance()->getLink('NewsOverview', array('application' => 'news')), true);
			MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'website', true);
			MetaTagHandler::getInstance()->addTag('og:title', 'og:title', WCF::getLanguage()->get(PAGE_TITLE), true);
			MetaTagHandler::getInstance()->addTag('og:description', 'og:description', WCF::getLanguage()->get(PAGE_DESCRIPTION), true);
			MetaTagHandler::getInstance()->addTag('generator', 'generator', 'voolia News-System');
		}
	}

	/**
	 * @see	\wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		if ($this->categoryID) {
			$this->objectList = new NewsCategoryList(array($this->categoryID));
		} else if (NEWS_INDEX_CATEGORIES) {
			$categoryIDs = array_intersect(explode("\n", NEWS_INDEX_CATEGORIES), NewsCategory::getAccessibleCategoryIDs());
			$this->objectList = new NewsCategoryList($categoryIDs);
		} else {
			parent::initObjectList();

			if ($this->user) {
				$this->objectList->getConditionBuilder()->add('news.userID = ?', array($this->user->userID));
			}
		}

		// exclude archives news from overview.
		$this->objectList->getConditionBuilder()->add('news.isArchived = 0');

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
		DashboardHandler::getInstance()->loadBoxes('de.voolia.news.NewsOverviewPage', $this);

		WCF::getTPL()->assign(array(
			'letters' => str_split(self::$availableLetters),
			'letter' => $this->letter,
			'stats' => $this->stats,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.voolia.news.entry')),
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.voolia.news.NewsOverviewPage'),
			'sidebarName' => 'de.voolia.news.NewsOverviewPage',
			'usersOnlineList' => $this->usersOnlineList,
			'statistics' => $this->statistics,
			'user' => $this->user,
			'userID' => $this->userID,
			'allowSpidersToIndexThisPage' => true
		));
	}
}
