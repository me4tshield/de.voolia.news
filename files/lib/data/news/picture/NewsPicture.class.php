<?php
namespace news\data\news\picture;
use news\data\NewsDatabaseObject;
use wcf\system\WCF;

/**
 * Represents a news picture.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPicture extends NewsDatabaseObject {
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
		if ($this->pictureID) {
			return NEWS_DIR .'images/news/'. (($this->categoryID) ? $this->categoryID.'/' : '') . $this->getFilename();
		}
		return false;
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * Returns the url to this picture.
	 * 
	 * @return	string
	 */
	public function getURL() {
		if ($this->pictureID) {
			return WCF::getPath('news') .'images/news/'. (($this->categoryID) ? $this->categoryID.'/' : '') . $this->getFilename();
		}
		return WCF::getPath('news') .'images/news/dummyPicture.png';
	}
}
