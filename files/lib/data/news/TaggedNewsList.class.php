<?php 
namespace news\data\news;
use news\data\category\NewsCategory;
use wcf\data\tag\Tag;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * Represents a list of news entries with tags.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class TaggedNewsList extends AccessibleNewsList {
	/**
	 * Creates a new TaggedNewsList object.
	 * 
	 * @param	\wcf\data\tag\Tag	$tag
	 */
	public function __construct(Tag $tag) {
		ViewableNewsList::__construct();

		$this->getConditionBuilder()->add('tag_to_object.objectTypeID = ? AND tag_to_object.languageID = ? AND tag_to_object.tagID = ?', array(TagEngine::getInstance()->getObjectTypeID('de.voolia.news.entry'), $tag->languageID, $tag->tagID));
		$this->getConditionBuilder()->add('news.newsID = tag_to_object.objectID');

		// accessible news categories
		$accessibleCategoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if (!empty($accessibleCategoryIDs)) $this->getConditionBuilder()->add('news.newsID IN (SELECT newsID FROM news'.WCF_N.'_news_to_category WHERE categoryID IN (?))', array(NewsCategory::getAccessibleCategoryIDs()));
		else $this->getConditionBuilder()->add('1=0');
	}

	/**
	 * @see	\wcf\data\DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_tag_to_object tag_to_object,
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
		$sql = "SELECT	tag_to_object.objectID
			FROM	wcf".WCF_N."_tag_to_object tag_to_object,
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
