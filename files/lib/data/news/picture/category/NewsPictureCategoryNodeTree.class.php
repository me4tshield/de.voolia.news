<?php
namespace news\data\news\picture\category;
use wcf\data\category\CategoryNodeTree;

/**
 * Represents a news picture category node tree.
 * 
 * @author	Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCategoryNodeTree extends CategoryNodeTree {
	/**
	 * name of the category node class
	 * @var	string
	 */
	protected $nodeClassName = 'news\data\news\picture\category\NewsPictureCategoryNode';
}
