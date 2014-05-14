<?php
namespace news\data\media\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a media category node.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MediaCategoryNode extends CategoryNode {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'news\data\media\category\MediaCategory';
}
