<?php
namespace news\data\news\picture\category;
use wcf\data\category\AbstractDecoratedCategory;

/**
 * Represents a news picture category.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCategory extends AbstractDecoratedCategory {
	/**
	 * object type name for news categories
	 * @var	string
	 */
	const OBJECT_TYPE_NAME = 'de.voolia.news.picture.category';
}
