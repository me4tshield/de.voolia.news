<?php
namespace news\system\user\notification\object;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\user\notification\object\IUserNotificationObject;

/**
 * Represents a news entry as a notification object.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'news\data\news\News';

	/**
	 * @see	\wcf\system\user\notification\object\IUserNotificationObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->newsID;
	}

	/**
	 * @see	\wcf\system\user\notification\object\IUserNotificationObject::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see	\wcf\system\user\notification\object\IUserNotificationObject::getURL()
	 */
	public function getURL() {
		return $this->getLink();
	}

	/**
	 * @see	\wcf\system\user\notification\object\IUserNotificationObject::getAuthorID()
	 */
	public function getAuthorID() {
		return $this->userID;
	}
}
