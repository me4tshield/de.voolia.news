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

	/**
	 * Returns the physical file name of this picture.
	 *
	 * @return	string
	 */
	public function getFilename() {
		return $this->fileHash .'.'. $this->fileExtension;
	}

	/**
	 * Returns the physical location of this picture.
	 * 
	 * @return	string
	 */
	public function getLocation() {
		return NEWS_DIR .'images/media/'. (($this->categoryID) ? $this->categoryID.'/' : '') . $this->getFilename();
	}

	/**
	 * Returns the url to this picture.
	 * 
	 * @return	string
	 */
	public function getURL() {
		return WCF::getPath('news') .'images/media/'. (($this->categoryID) ? $this->categoryID.'/' : '') . $this->getFilename();
	}
}
