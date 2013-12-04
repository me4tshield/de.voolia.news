<?php
namespace news\system\user\notification\event;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event for new news entries.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('news.entry.notification.title');
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('news.entry.notification.message', array(
			'news' => $this->userNotificationObject,
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		return $this->getLanguage()->getDynamicVariable('news.entry.notification.mail', array(
			'news' => $this->userNotificationObject,
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'object' => $this->userNotificationObject,
		), '#comments');
	}
}
