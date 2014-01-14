<?php
namespace news\acp\page;
use wcf\acp\page\AbstractCategoryListPage;

/**
 * Shows the news picture category list page.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCategoryListPage extends AbstractCategoryListPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.picture.category.list';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.voolia.news.picture.category';

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$pageTitle
	 */
	public $pageTitle = 'news.acp.menu.link.news.picture.category.list';
}
