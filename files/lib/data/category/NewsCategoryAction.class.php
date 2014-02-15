<?php 
namespace news\data\category;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents news category actions.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\category\CategoryEditor';

	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$allowGuestAccess
	 */
	protected $allowGuestAccess = array('markAllAsRead');

	/**
	 * Validates the mark all as read action.
	 */
	public function validateMarkAllAsRead() { /* nothing */ }

	/**
	 * Marks all news categories as read.
	 */
	public function markAllAsRead() {
		VisitTracker::getInstance()->trackTypeVisit('de.voolia.news.entry');

		// reset the user storage data and delete notifications
		if (WCF::getUser()->userID) {
			// user storage data
			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'newsUnreadEntries');
			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'newsUnreadWatchedEntries');

			// user notifications
			UserNotificationHandler::getInstance()->deleteNotifications('news', 'de.voolia.news.entry', array(WCF::getUser()->userID));
		}
	}
}
