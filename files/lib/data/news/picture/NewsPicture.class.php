<?php
namespace news\data\news\picture;
use news\data\NewsDatabaseObject;
use wcf\system\WCF;

/**
 * Represents a news picture.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPicture extends NewsDatabaseObject implements INewsPicture {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'news_picture';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'pictureID';

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
