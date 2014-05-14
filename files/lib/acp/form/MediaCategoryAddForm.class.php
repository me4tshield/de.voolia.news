<?php
namespace news\acp\form;
use wcf\acp\form\AbstractCategoryAddForm;
use wcf\util\FileUtil;

/**
 * Shows the media category add form.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MediaCategoryAddForm extends AbstractCategoryAddForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.media.category.add';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.voolia.news.media.category';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$pageTitle
	 */
	public $pageTitle = 'news.acp.menu.link.media.category.add';

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		$returnValues = $this->objectAction->getReturnValues();

		// create folder for media objects of this category
		FileUtil::makePath(NEWS_DIR.'images/media/'. $returnValues['returnValues']->categoryID .'/');
	}
}
