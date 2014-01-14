<?php 
namespace news\page;
use news\system\NEWSCore;
use wcf\page\MultipleLinkPage;
use wcf\system\WCF;

/**
 * Shows a list of unread news.
 *
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class UnreadNewsListPage extends NewsOverviewPage {
	/**
	 * @see wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'news\data\news\UnreadNewsList';

	/**
	 * @see wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'newsUnreadEntries';

	/**
	 * @see	wcf\page\IPage::readData()
	 */
	public function readData() {
		MultipleLinkPage::readData();

		NEWSCore::getInstance()->setBreadcrumbs();
	}
	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		MultipleLinkPage::assignVariables();

		WCF::getTPL()->assign(array(
			'controller' => 'UnreadNewsList'
		));
	}
}
