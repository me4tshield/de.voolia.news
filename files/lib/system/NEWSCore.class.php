<?php
namespace news\system;
use news\data\category\NewsCategory;
use news\data\news\News;
use wcf\system\application\AbstractApplication;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * This class extends the main WCF class with news specific functions.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NEWSCore extends AbstractApplication {
	/**
	 * @see	\wcf\system\application\AbstractApplication::$abbreviation
	 */
	protected $abbreviation = 'news';

	/**
	 * @see	\wcf\system\application\IApplication::__run()
	 */
	public function __run() { /* nothing */ }

	/**
	 * Sets the breadcrumbs.
	 * 
	 * @param	\news\data\category\NewsCategory	$category
	 * @param	\news\data\news\News			$news
	 */
	public function setBreadcrumbs(array $parentCategories = array(), NewsCategory $category = null, News $news = null) {
		if (PageMenu::getInstance()->getLandingPage()->menuItem != 'news.header.menu.news') {
			WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('news.header.menu.news'), LinkHandler::getInstance()->getLink('NewsOverview', array(
				'application' => 'news'
			))));
		}

		foreach ($parentCategories as $parentCategory) {
			WCF::getBreadcrumbs()->add($parentCategory->getBreadcrumb());
		}

		if ($category !== null) {
			WCF::getBreadcrumbs()->add($category->getBreadcrumb());
		}

		if ($news !== null) {
			if ($news->isArchived) {
				WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('news.header.menu.news.archive'), LinkHandler::getInstance()->getLink('NewsArchive', array(
					'application' => 'news'
				))));
			}

			WCF::getBreadcrumbs()->add($news->getBreadcrumb());
		}
	}
}
