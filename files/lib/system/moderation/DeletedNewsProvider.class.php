<?php
namespace news\system\moderation;
use news\data\news\DeletedNewsList;
use wcf\system\moderation\IDeletedContentProvider;

/**
 * Implementation of IDeletedContentProvider for deleted news.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class DeletedNewsProvider implements IDeletedContentProvider {
	/**
	 * @see	\wcf\system\moderation\IDeletedContentProvider::getObjectList()
	 */
	public function getObjectList() {
		return new DeletedNewsList();
	}

	/**
	 * @see	\wcf\system\moderation\IDeletedContentProvider::getTemplateName()
	 */
	public function getTemplateName() {
		return 'newsMessageList';
	}

	/**
	 * @see	\wcf\system\moderation\IDeletedContentProvider::getApplication()
	 */
	public function getApplication() {
		return 'news';
	}
}
