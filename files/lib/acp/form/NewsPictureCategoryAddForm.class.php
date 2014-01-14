<?php
namespace news\acp\form;
use wcf\acp\form\AbstractCategoryAddForm;
use wcf\util\FileUtil;

/**
 * Shows the news picture category add form.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCategoryAddForm extends AbstractCategoryAddForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.picture.category.add';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.voolia.news.picture.category';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$pageTitle
	 */
	public $pageTitle = 'news.acp.menu.link.news.picture.category.add';

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		$returnValues = $this->objectAction->getReturnValues();

		// create folder for pictures of this category
		FileUtil::makePath(NEWS_DIR.'images/news/'. $returnValues['returnValues']->categoryID .'/');
	}
}
