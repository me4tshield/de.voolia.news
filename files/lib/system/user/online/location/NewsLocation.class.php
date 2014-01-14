<?php
namespace news\system\user\online\location;
use news\data\category\NewsCategory;
use news\data\news\NewsList;
use wcf\data\user\online\UserOnline;
use wcf\system\user\online\location\IUserOnlineLocation;
use wcf\system\WCF;

/**
 * Implementation of IUserOnlineLocation for news entries.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsLocation implements IUserOnlineLocation {
	/**
	 * news ids
	 * @var	array<integer>
	 */
	protected $newsIDs = array();

	/**
	 * list of news entries
	 * @var	array<\news\data\news\News>
	 */
	protected $news = null;

	/**
	 * @see	\wcf\system\user\online\location\IUserOnlineLocation::cache()
	 */
	public function cache(UserOnline $user) {
		if ($user->objectID) $this->newsIDs[] = $user->objectID;
	}

	/**
	 * @see	\wcf\system\user\online\location\IUserOnlineLocation::get()
	 */
	public function get(UserOnline $user, $languageVariable = '') {
		if ($this->news === null) {
			$this->loadNews();
		}

		if (!isset($this->news[$user->objectID])) {
			return '';
		}

		return WCF::getLanguage()->getDynamicVariable($languageVariable, array('news' => $this->news[$user->objectID]));
	}

	/**
	 * Loads the news entries.
	 */
	protected function loadNews() {
		$this->news = array();

		if (empty($this->newsIDs)) return;
		$this->newsIDs = array_unique($this->newsIDs);
		$categoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if (empty($categoryIDs)) return;

		$newsList = new NewsList();
		$newsList->getConditionBuilder()->add('news.newsID IN (?)', array($this->newsIDs));
		$newsList->getConditionBuilder()->add('news.newsID IN (SELECT newsID FROM news'.WCF_N.'_news_to_category WHERE categoryID IN (?))', array($categoryIDs));

		// default conditions
		if (!WCF::getSession()->getPermission('mod.news.canReadDeactivatedNews')) $newsList->getConditionBuilder()->add('news.isActive = 1');
		if (!WCF::getSession()->getPermission('mod.news.canReadDeletedNews')) $newsList->getConditionBuilder()->add('news.isDeleted = 0');

		if (!WCF::getSession()->getPermission('mod.news.canReadFutureNews')) {
			if (WCF::getUser()->userID) $newsList->getConditionBuilder()->add('(news.isPublished = 1 OR news.userID = ?)', array(WCF::getUser()->userID));
			else $newsList->getConditionBuilder()->add('news.isPublished = 1');
		}

		$newsList->readObjects();
		$this->news = $newsList->getObjects();
	}
}
