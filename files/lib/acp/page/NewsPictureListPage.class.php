<?php
namespace news\acp\page;
use news\data\news\picture\category\NewsPictureCategoryNodeTree;
use news\data\news\picture\GroupedNewsPictureList;
use wcf\page\AbstractPage;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\NamedUserException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the news picture list page.
 * 
 * @author	Pascal Bade, Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureListPage extends AbstractPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.picture.list';

	/**
	 * category id
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * category object
	 * @var	\news\data\news\picture\category\NewsPictureCategory
	 */
	public $category = null;

	/**
	 * category list
	 * @var	array<\wcf\data\category\Category>
	 */
	public $categoryList = null;

	/**
	 * grouped picture list
	 * @var	array
	 */
	public $pictureList = null;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		$categories = CategoryHandler::getInstance()->getCategories('de.voolia.news.picture.category');

		if (isset($_REQUEST['id'])) {
			$this->categoryID = intval($_REQUEST['id']);
			$this->category = CategoryHandler::getInstance()->getCategory($this->categoryID);
		} else {
			while (!empty($categories) && !$this->categoryID) {
				$category = array_shift($categories);
				if ($category->parentCategoryID == 0) {
					$this->categoryID = $category->categoryID;
					$this->category = $category;
				}
			}
		}

		// check category
		if (!empty($categories) && ($this->category === null || $this->category->parentCategoryID != 0)) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		if (!$this->categoryID) return;

		// categories
		$categoryTree = new NewsPictureCategoryNodeTree('de.voolia.news.picture.category');
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);

		// pictures
		$categoryIDs = array($this->categoryID);
		foreach ($this->category->getChildCategories() as $childCategory) {
			$categoryIDs[] = $childCategory->categoryID;
		}

		$this->pictureList = new GroupedNewsPictureList();
		$this->pictureList->getConditionBuilder()->add('news_picture.categoryID IN (?)', array($categoryIDs));
		$this->pictureList->readObjects();
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'categoryList' => $this->categoryList,
			'pictures' => ($this->categoryID) ? $this->pictureList->getObjects() : array()
		));
	}
}
