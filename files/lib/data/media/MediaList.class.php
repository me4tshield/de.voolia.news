<?php
namespace news\data\media;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of media objects.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 * @subpackage	data.media
 * @category	voolia News
 */
class MediaList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'news\data\media\Media';
}
