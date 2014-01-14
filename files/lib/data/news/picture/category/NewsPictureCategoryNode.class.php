<?php
namespace news\data\news\picture\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a news picture category node.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCategoryNode extends CategoryNode {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'news\data\news\picture\category\NewsPictureCategory';
}
