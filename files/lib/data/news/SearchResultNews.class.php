<?php
namespace news\data\news;
use wcf\data\search\ISearchResultObject;
use wcf\system\search\SearchResultTextParser;

/**
 * Represents news search results.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class SearchResultNews extends ViewableNews implements ISearchResultObject {
	/**
	 * @see	\wcf\data\news\News::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::getInstance()->parse($this->getDecoratedObject()->getExcerpt());
	}

	/**
	 * @see	\wcf\data\search\ISearchResultObject::getSubject()
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @see	\wcf\data\search\ISearchResultObject::getLink()
	 */
	public function getLink($query = '') {
		return $this->getDecoratedObject()->getLink();
	}

	/**
	 * @see	\wcf\data\search\ISearchResultObject::getTime()
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @see	\wcf\data\search\ISearchResultObject::getObjectTypeName()
	 */
	public function getObjectTypeName() {
		return 'de.voolia.news.entry';
	}

	/**
	 * @see	\wcf\data\search\ISearchResultObject::getContainerTitle()
	 */
	public function getContainerTitle() {
		return '';
	}

	/**
	 * @see	\wcf\data\search\ISearchResultObject::getContainerLink()
	 */
	public function getContainerLink() {
		return '';
	}
}
