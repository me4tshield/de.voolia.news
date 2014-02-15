<?php
namespace news\acp\form;
use wcf\acp\form\AbstractCategoryEditForm;

/**
 * Shows the news category edit form.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryEditForm extends AbstractCategoryEditForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.category.list';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.voolia.news.category';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$title
	 */
	public $pageTitle = 'news.acp.menu.link.news.category.edit';
}
