<?php
namespace news\data\media\category;
use wcf\data\category\CategoryNodeTree;

/**
 * Represents a media category node tree.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MediaCategoryNodeTree extends CategoryNodeTree {
	/**
	 * name of the category node class
	 * @var	string
	 */
	protected $nodeClassName = 'news\data\media\category\MediaCategoryNode';
}
