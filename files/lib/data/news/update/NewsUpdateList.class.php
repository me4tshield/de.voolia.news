<?php
namespace news\data\news\update;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of news updates.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdateList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'news\data\news\update\NewsUpdate';
}
