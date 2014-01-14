<?php
namespace news\system\search;
use news\data\category\NewsCategory;
use news\data\news\SearchResultNewsList;
use wcf\form\IForm;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\search\AbstractSearchableObjectType;
use wcf\system\WCF;

/**
 * Implementation of the search function for news entries
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsSearch extends AbstractSearchableObjectType {
	/**
	 * message data cache
	 * @var	array
	 */
	public $messageCache = array();

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::cacheObjects()
	 */
	public function cacheObjects(array $objectIDs, array $additionalData = null) {
		$newsList = new SearchResultNewsList();

		$newsList->getConditionBuilder()->add('news.newsID IN (?)', array($objectIDs));
		$newsList->readObjects();
		foreach ($newsList->getObjects() as $news) {
			$this->messageCache[$news->newsID] = $news;
		}
	}

	/**
	 * @see	\wcf\system\search\AbstractSearchableObjectType::getApplication()
	 */
	public function getApplication() {
		return 'news';
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getObject()
	 */
	public function getObject($objectID) {
		if (isset($this->messageCache[$objectID])) return $this->messageCache[$objectID];
		return null;
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getTableName()
	 */
	public function getTableName() {
		return 'news'.WCF_N.'_news';
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getIDFieldName()
	 */
	public function getIDFieldName() {
		return $this->getTableName().'.newsID';
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getConditions()
	 */
	public function getConditions(IForm $form = null) {
		$conditionBuilder = new PreparedStatementConditionBuilder();

		// accessible news category ids
		$categoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if (empty($categoryIDs)) {
			throw new PermissionDeniedException();
		}
		$conditionBuilder->add($this->getTableName().'.newsID IN (SELECT newsID FROM news'.WCF_N.'_news_to_category WHERE categoryID IN (?))', array($categoryIDs));

		// set default conditions for news
		$conditionBuilder->add($this->getTableName().'.isActive = 1');
		$conditionBuilder->add($this->getTableName().'.isDeleted = 0');

		if (WCF::getUser()->userID) {
			if (!WCF::getSession()->getPermission('mod.news.canReadFutureNews')) {
				$conditionBuilder->add($this->getTableName().'.isPublished = 1 OR '.$this->getTableName().'.userID = ?', array(WCF::getUser()->userID));
			}
		} else {
			$conditionBuilder->add($this->getTableName().'.isPublished = 1');
		}

		return $conditionBuilder;
	}
}
