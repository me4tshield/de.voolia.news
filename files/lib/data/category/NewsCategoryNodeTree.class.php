<?php
namespace news\data\category;
use wcf\data\category\CategoryNode;
use wcf\data\category\CategoryNodeTree;

/**
 * Represents a news category node tree.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryNodeTree extends CategoryNodeTree {
	/**
	 * name of the category node class
	 * @var	string
	 */
	protected $nodeClassName = 'news\data\category\NewsCategoryNode';

	/**
	 * @see	\wcf\data\category\CategoryNodeTree::isIncluded()
	 */
	public function isIncluded(CategoryNode $categoryNode) {
		return parent::isIncluded($categoryNode) && $categoryNode->isAccessible();
	}
}
