<?php
namespace news\acp\form;
use news\data\news\picture\category\NewsPictureCategoryNodeTree;
use news\data\news\picture\NewsPicture;
use news\data\news\picture\NewsPictureEditor;
use wcf\form\AbstractForm;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the news picture add form.
 * 
 * @author	Pascal Bade, Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.picture.add';

	/**
	 * category id
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * category list
	 * @var	array<\wcf\data\category\Category>
	 */
	public $categoryList = null;

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
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);

		if (isset($_REQUEST['id'])) $this->pictureID = intval($_REQUEST['id']);
		if ($this->pictureID) {
			$this->picture = new NewsPicture($this->pictureID);
			if (!$this->picture->pictureID) {
				throw new IllegalLinkException();
			}
		}
	}

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();

		// validate picture
		if (!$this->picture) {
			throw new UserInputException('picture');
		}

		// validate category id
		$category = CategoryHandler::getInstance()->getCategory($this->categoryID);
		if ($category === null) {
			throw new UserInputException('categoryID');
		}

		// validate title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		// update picture
		$this->picture = new NewsPictureEditor($this->picture);
		$this->picture->update(array(
			'categoryID' => $this->categoryID,
			'title' => $this->title
		));

		if (@copy($this->picture->getLocation(), NEWS_DIR .'/images/news/'. $this->categoryID .'/'. $this->picture->getFilename())) {
			@unlink($this->picture->getLocation());
		} else {
			throw new UserInputException('picture', 'savingFailed');
		}

		$this->saved();
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$categoryTree = new NewsPictureCategoryNodeTree('de.voolia.news.picture.category');
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'add',
			'title' => $this->title,
			'categoryID' => $this->categoryID,
			'categoryList' => $this->categoryList,
			'pictureID' => $this->pictureID,
			'picture' => $this->picture
		));
	}
}
