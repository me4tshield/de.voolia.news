<?php
namespace news\form;
use news\data\media\category\MediaCategoryNodeTree;
use news\data\media\Media;
use news\data\media\MediaAction;
use news\data\media\MediaEditor;
use news\data\media\MediaList;
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
	 * picture title
	 * @var	string
	 */
	public $title = '';

	/**
	 * media sort field
	 * @var	string
	 */
	public $type = '';

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['categoryID'])) $this->categoryID = intval($_POST['categoryID']);
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
	}

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);

		if (isset($_REQUEST['type'])) $this->type = StringUtil::trim($_REQUEST['type']);

		if (isset($_REQUEST['id'])) $this->pictureID = intval($_REQUEST['id']);
		if ($this->pictureID) {
			$this->picture = new Media($this->pictureID);
			if (!$this->picture->pictureID) {
				throw new IllegalLinkException();
			}
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
		if ($this->type) {
			$this->mediaList->getConditionBuilder()->add('news_media.type = ?', array($this->type));
		}
		$this->mediaList->readObjects();
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		$oldLocation = $this->picture->getLocation();

		// update picture
		$pictureEditor = new MediaEditor($this->picture);
		$pictureEditor->update(array(
			'categoryID' => $this->categoryID,
			'title' => $this->title
		));

		// reload news picture
		$this->picture = new Media($this->pictureID);

		if ($oldLocation != $this->picture->getLocation()) {
			if (@copy($oldLocation, $this->picture->getLocation())) {
				@unlink($oldLocation);
			} else {
				throw new UserInputException('picture', 'savingFailed');
			}
		}

		$this->saved();

		// show success
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'objects' => $this->mediaList,
			'category' => $this->category,
			'action' => 'add',
			'title' => $this->title,
			'categoryID' => $this->categoryID,
			'categoryList' => $this->categoryList,
			'pictureID' => $this->pictureID,
			'picture' => $this->picture,
			'type' => $this->type,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.voolia.news.MediaManagementPage'),
			'sidebarName' => 'de.voolia.news.MediaManagementPage',
			'allowSpidersToIndexThisPage' => false
		));
	}
}
