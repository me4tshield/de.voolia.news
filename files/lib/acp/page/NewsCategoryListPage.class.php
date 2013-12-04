<?php
namespace news\acp\page;
use wcf\acp\page\AbstractCategoryListPage;

/**
 * Shows the category list page.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryListPage extends AbstractCategoryListPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.category.list';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.voolia.news.category';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$pageTitle
	 */
	public $pageTitle = 'news.acp.menu.link.news.category.list';
}
