<?php
namespace news\system\user\object\watch;
use news\data\category\NewsCategory;
use wcf\data\object\type\AbstractObjectTypeProcessor;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\object\watch\IUserObjectWatch;

/**
 * Implementation of IUserObjectWatch for watched news categories.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryUserObjectWatch extends AbstractObjectTypeProcessor implements IUserObjectWatch {
	/**
	 * @see	\wcf\system\user\object\watch\IUserObjectWatch::validateObjectID()
	 */
	public function validateObjectID($objectID) {
		$category = CategoryHandler::getInstance()->getCategory($objectID);
		if ($category === null) {
			throw new IllegalLinkException();
		}
		$category = new NewsCategory($category);
		if (!$category->isAccessible()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\system\user\object\watch\IUserObjectWatch::resetUserStorage()
	 */
	public function resetUserStorage(array $userIDs) {
		UserStorageHandler::getInstance()->reset($userIDs, 'newsUnreadWatchedEntries');
		UserStorageHandler::getInstance()->reset($userIDs, 'newsSubscribedCategories');
	}
}
