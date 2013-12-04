<?php
namespace news\acp\form;
use wcf\acp\form\AbstractCategoryEditForm;

/**
 * Shows the news picture category edit form.
 * 
 * @author	Pascal Bade, Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCategoryEditForm extends AbstractCategoryEditForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.picture.category.list';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.voolia.news.picture.category';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$title
	 */
	public $pageTitle = 'news.acp.menu.link.news.picture.category.edit';
}
