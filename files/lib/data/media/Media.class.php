<?php
namespace news\data\media;
use news\data\NewsDatabaseObject;
use wcf\util\StringUtil;

/**
 * Represents a media object.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 * @subpackage	data.media
 * @category	voolia News
 */
class Media extends NewsDatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'news_media';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'objectID';
}
