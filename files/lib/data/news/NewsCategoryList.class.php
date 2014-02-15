<?php 
namespace news\data\news;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryList extends AccessibleNewsList {
	/**
	 * Creates a new NewsCategoryList object.
	 * 
	 * @param	array<integer>		$categoryIDs
	 */
	public function __construct(array $categoryIDs) {
		ViewableNewsList::__construct();

		// accessible news categories
		if (!empty($categoryIDs)) {
			$this->getConditionBuilder()->add('news_to_category.categoryID IN (?)', array($categoryIDs));
			$this->getConditionBuilder()->add('news.newsID = news_to_category.newsID');
		} else $this->getConditionBuilder()->add('1=0');

		// default conditions
		if (!WCF::getSession()->getPermission('mod.news.canReadDeactivatedNews')) $this->getConditionBuilder()->add('news.isActive = 1');
		if (!WCF::getSession()->getPermission('mod.news.canReadDeletedNews')) $this->getConditionBuilder()->add('news.isDeleted = 0');

		if (!WCF::getSession()->getPermission('mod.news.canReadFutureNews')) {
			if (WCF::getUser()->userID) $this->getConditionBuilder()->add('(news.isPublished = 1 OR news.userID = ?)', array(WCF::getUser()->userID));
			else $this->getConditionBuilder()->add('news.isPublished = 1');
		}

		// apply language filter
		if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
			$this->getConditionBuilder()->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(WCF::getUser()->getLanguageIDs()));
		}
	}

	/**
	 * @see	\wcf\data\DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	news".WCF_N."_news_to_category news_to_category,
				news".WCF_N."_news news
			".$this->sqlConditionJoins."
			".$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjectIDs()
	 */
	public function readObjectIDs() {
		$this->objectIDs = array();
		$sql = "SELECT	news_to_category.newsID AS objectID
			FROM	news".WCF_N."_news_to_category news_to_category,
				news".WCF_N."_news news
				".$this->sqlConditionJoins."
				".$this->getConditionBuilder()."
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()->getParameters());
		while ($row = $statement->fetchArray()) {
			$this->objectIDs[] = $row['objectID'];
		}
	}
}
