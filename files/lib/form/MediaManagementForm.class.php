<?php
namespace news\form;
use news\data\media\Media;
use news\data\media\MediaList;
use news\data\media\category\MediaCategoryNodeTree;
use wcf\form\AbstractForm;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the media management page.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MediaManagementForm extends AbstractForm {
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
	 * category list
	 * @var	array<\wcf\data\category\Category>
	 */
	public $categoryList = null;

	/**
	 * @see	\wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = 10;

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
	 * List with all media objects
	 * @var	\news\data\media\MediaList
	 */
	public $mediaList = null;

	/**
	 * picture id
	 * @var	integer
	 */
	public $pictureID = 0;

	/**
	 * picture object
	 * @var	\news\data\news\picture\NewsPicture
	 */
	public $picture = null;

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

		// more news from this category
		$this->mediaList = new MediaList();
		$this->mediaList->readObjects();
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();


	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'objects' => $this->mediaList,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList,
			'pictureID' => $this->pictureID,
			'picture' => $this->picture,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.voolia.news.MediaManagementPage'),
			'sidebarName' => 'de.voolia.news.MediaManagementPage',
			'allowSpidersToIndexThisPage' => false
		));
	}
}
