<?php
namespace news\system\tagging;
use news\data\news\TaggedNewsList;
use wcf\data\tag\Tag;
use wcf\system\tagging\ITaggable;

/**
 * Implementation of the tagging function for news entries
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class TaggableNews implements ITaggable {
	/**
	 * @see	\wcf\system\tagging\ITaggable::getObjectList()
	 */
	public function getObjectList(Tag $tag) {
		return new TaggedNewsList($tag);
	}

	/**
	 * @see	\wcf\system\tagging\ITaggable::getTemplateName()
	 */
	public function getTemplateName() {
		return 'newsMessageList';
	}

	/**
	 * @see	\wcf\system\tagging\ITaggable::getApplication()
	 */
	public function getApplication() {
		return 'news';
	}
}
