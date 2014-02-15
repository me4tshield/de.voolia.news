<?php
namespace news\data\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a news category node
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryNode extends CategoryNode {
	/**
	 * number of news
	 * @var	integer
	 */
	protected $news = null;

	/**
	 * number of unread news
	 * @var	integer
	 */
	protected $unreadNews = null;

	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'news\data\category\NewsCategory';

	/**
	 * Returns the depth of this node.
	 * 
	 * @var	integer
	 */
	public function getDepth() {
		$element = $this;
		$i = 0;

		while ($element->parentNode->parentNode != null) {
			$i++;
			$element = $element->parentNode;
		}

		return $i;
	}

	/**
	 * Returns the number of news in this category.
	 * 
	 * @return	integer
	 */
	public function getNews() {
		if ($this->news === null) {
			$this->news = NewsCategoryCache::getInstance()->getNews($this->categoryID);
		}

		return $this->news;
	}

	/**
	 * Returns number of unread news.
	 * 
	 * @return integer
	 */
	public function getUnreadNews() {
		if ($this->unreadNews === null) {
			$this->unreadNews = NewsCategoryCache::getInstance()->getUnreadNews($this->categoryID);
		}

		return $this->unreadNews;
	}
}
