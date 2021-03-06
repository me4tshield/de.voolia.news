<?php
namespace news\data\news;

/**
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsFeedList extends NewsCategoryList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$decoratorClassName
	 */
	public $decoratorClassName = 'news\data\news\NewsFeed';
}
