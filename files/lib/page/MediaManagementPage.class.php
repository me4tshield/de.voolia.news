<?php
namespace news\page;
use news\data\media\category\MediaCategory;
use news\data\media\category\MediaCategoryNodeTree;
use wcf\page\MultipleLinkPage;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

/**
 * Shows the media management page.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MediaManagementPage extends MultipleLinkPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.header.menu.news';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('user.news.canViewNews'); // TODO: Add new permission

	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_CONTENT_NEWS');

	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'news\data\media\MediaList';

	/**
	 * category list
	 * @var	array<\wcf\data\category\Category>
	 */
	public $categoryList = null;

	/**
	 * @see	\wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = 100;

	/**
	 * @see	\wcf\page\MultipleLinkPage::$sortField
	 */
	public $sortField = 'name';

	/**
	 * @see	\wcf\page\MultipleLinkPage::$sortOrder
	 */
	public $sortOrder = 'ASC';

	/**
	 * category
	 * @var	\wcf\data\category\Category
	 */
	public $category = null;

	/**
	 * category id
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// media by category
		if (isset($_REQUEST['id'])) {
			// get category id
			$this->categoryID = intval($_REQUEST['id']);

			// get category by id
			$this->category = CategoryHandler::getInstance()->getCategory($this->categoryID);
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$categoryTree = new MediaCategoryNodeTree('de.voolia.news.media.category');
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);
	}

	/**
	 * @see	\wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList();
			if ($this->categoryID) {
				$this->objectList->getConditionBuilder()->add('news_media.categoryID IN (?)', array($this->categoryID));
			}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.voolia.news.MediaManagementPage'),
			'sidebarName' => 'de.voolia.news.MediaManagementPage',
			'allowSpidersToIndexThisPage' => false
		));
	}
}
