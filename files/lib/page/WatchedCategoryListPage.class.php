<?php 
namespace news\page;
use news\data\category\NewsCategory;
use news\data\news\NewsCategoryList;
use wcf\system\WCF;

/**
 * Shows a list of news from watched categories.
 *
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class WatchedCategoryListPage extends NewsOverviewPage {
	/**
	 * @see	\wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		$this->objectList = new NewsCategoryList(NewsCategory::getSubscribedCategoryIDs());
	}
}
