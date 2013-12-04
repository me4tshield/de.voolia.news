<?php
namespace news\data\category;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manage the news category cache.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryCache extends SingletonFactory {
	/**
	 * number of total news
	 * @var	array<integer>
	 */
	protected $news = null;

	/**
	 * number of unread news
	 * @var	array<integer>
	 */
	protected $unreadNews = null;

	/**
	 * Calculates the number of news.
	 */
	protected function initNews() {
		$this->news = array();

		// add default conditions
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('news.isActive = 1');
		$conditionBuilder->add('news.isDeleted = 0');
		$conditionBuilder->add('news.isPublished = 1');

		// apply language filter for news
		if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
			$conditionBuilder->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(WCF::getUser()->getLanguageIDs()));
		}

		$sql = "SELECT 		COUNT(*) AS count, news_to_category.categoryID
			FROM 		news".WCF_N."_news news
			LEFT JOIN 	news".WCF_N."_news_to_category news_to_category
			ON 		(news_to_category.newsID = news.newsID)
			".$conditionBuilder."
			GROUP BY	news_to_category.categoryID";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			$this->news[$row['categoryID']] = $row['count'];
		}
	}

	/**
	 * Gets the news.
	 */
	public function getNews($categoryID) {
		if ($this->news === null) {
			$this->initNews();
		}

		if (isset($this->news[$categoryID])) return $this->news[$categoryID];
		return 0;
	}

	/**
	 * Calculates the number of unread news.
	 */
	protected function initUnreadNews() {
		$this->unreadNews = array();

		if (WCF::getUser()->userID) {
			// add default conditions
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add('news.time > ?', array(VisitTracker::getInstance()->getVisitTime('de.voolia.news.entry')));
			$conditionBuilder->add('news.isActive = 1');
			$conditionBuilder->add('news.isDeleted = 0');
			$conditionBuilder->add('news.isPublished = 1');
			$conditionBuilder->add('tracked_visit.visitTime IS NULL');

			// apply language filter
			if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
				$conditionBuilder->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(WCF::getUser()->getLanguageIDs()));
			}

			$sql = "SELECT 		COUNT(*) AS count, news_to_category.categoryID
				FROM 		news".WCF_N."_news news
				LEFT JOIN 	wcf".WCF_N."_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.voolia.news.entry')." AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = ".WCF::getUser()->userID.")
				LEFT JOIN 	news".WCF_N."_news_to_category news_to_category ON (news_to_category.newsID = news.newsID)
				".$conditionBuilder."
				GROUP BY	news_to_category.categoryID";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditionBuilder->getParameters());
			while ($row = $statement->fetchArray()) {
				$this->unreadNews[$row['categoryID']] = $row['count'];
			}
		}
	}

	/**
	 * Gets the number of unread news.
	 * 
	 * @param	integer		$categoryID
	 * @return	integer
	 */
	public function getUnreadNews($categoryID) {
		if ($this->unreadNews === null) {
			$this->initUnreadNews();
		}

		if (isset($this->unreadNews[$categoryID])) return $this->unreadNews[$categoryID];
		return 0;
	}
}
