<?php
namespace news\system\user\notification\event;
use news\data\news\News;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event for news comments.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCommentUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('news.entry.comment.notification.title');
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		$news = new News($this->userNotificationObject->objectID);

		return $this->getLanguage()->getDynamicVariable('news.entry.comment.notification.message', array(
			'news' => $news,
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$news = new News($this->userNotificationObject->objectID);

		return $this->getLanguage()->getDynamicVariable('news.entry.comment.notification.mail', array(
			'news' => $news,
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		$news = new News($this->userNotificationObject->objectID);

		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'object' => $news
		), '#comments');
	}
}
