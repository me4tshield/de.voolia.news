<?php
namespace news\data\news;
use wcf\data\DatabaseObjectList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Represents a list of news.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'news\data\news\News';

	/**
	 * enables/disable the loading of categories
	 * @var	boolean
	 */
	protected $categoryLoading = true;

	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		parent::readObjects();

		// get news categories
		if ($this->categoryLoading) {
			if (!empty($this->objectIDs)) {
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('newsID IN (?)', array($this->objectIDs));
				$sql = "SELECT	*
					FROM	news".WCF_N."_news_to_category
					".$conditionBuilder;
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				while ($row = $statement->fetchArray()) {
					if (isset($this->objects[$row['newsID']])) $this->objects[$row['newsID']]->setCategoryID($row['categoryID']);
				}
			}
		}
	}

	/**
	 * Enables/disable the loading of news categories.
	 * 
	 * @param	boolean		$enable
	 */
	public function enableCategoryLoading($enable = true) {
		$this->categoryLoading = $enable;
	}
}
