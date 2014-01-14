<?php
namespace news\data\news\source;
use news\data\NewsDatabaseObject;
use wcf\util\StringUtil;

/**
 * Represents a news source.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsSource extends NewsDatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'news_source';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'sourceID';

	/**
	 * Returns the link of this source.
	 * @return	string
	 */
	public function getLink() {
		return StringUtil::getAnchorTag($this->sourceLink);
	}
}
