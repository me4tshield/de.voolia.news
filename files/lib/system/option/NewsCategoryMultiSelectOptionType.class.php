<?php
namespace news\system\option;
use wcf\system\option\AbstractCategoryMultiSelectOptionType;

/**
 * Option type implementation for multi select lists.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryMultiSelectOptionType extends AbstractCategoryMultiSelectOptionType {
	/**
	 * @see	\wcf\system\option\AbstractCategoryMultiSelectOptionType::$objectType
	 */
	public $objectType = 'de.voolia.news.category';

	/**
	 * @see	\wcf\system\option\AbstractCategoryMultiSelectOptionType::$nodeTreeClassname
	 */
	public $nodeTreeClassname = 'news\data\category\NewsCategoryNodeTree';
}
