<?php
namespace news\data\media\category;
use wcf\data\category\AbstractDecoratedCategory;

/**
 * Represents a media category.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MediaCategory extends AbstractDecoratedCategory {
	/**
	 * object type name for media categories
	 * @var	string
	 */
	const OBJECT_TYPE_NAME = 'de.voolia.news.media.category';
}
