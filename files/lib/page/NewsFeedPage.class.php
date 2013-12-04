<?php
namespace news\page;
use news\data\news\NewsFeedList;
use news\data\category\NewsCategory;
use wcf\page\AbstractFeedPage;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Show the news entries for a specified categories in rss feed.
 *
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsFeedPage extends AbstractFeedPage {
	/**
	 * number of news items per page
	 * @var	integer
	 */
	public $itemsPerPage = 20;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (empty($this->objectIDs)) {
			$this->objectIDs = NewsCategory::getAccessibleCategoryIDs();
		} else {
			foreach ($this->objectIDs as $objectID) {
				// get category
				$category = NewsCategory::getCategory($objectID);

				// check permissions and category
				if (!$category->isAccessible()) throw new PermissionDeniedException();
				if ($category === null) throw new IllegalLinkException();
			}
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$this->title = WCF::getLanguage()->get('news.header.menu.news');

		// read the news entries
		$this->items = new NewsFeedList($this->objectIDs);
		$this->items->sqlLimit = $this->itemsPerPage;
		$this->items->readObjects();

		// set the page title
		if (count($this->objectIDs) === 1) {
			$this->title = CategoryHandler::getInstance()->getCategory(reset($this->objectIDs))->getTitle();
		}
	}
}
