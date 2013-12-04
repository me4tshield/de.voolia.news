<?php
namespace news\system\user\notification\event;
use news\data\news\News;
use wcf\data\comment\Comment;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event for single news comments.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('news.entry.commentResponse.notification.title');
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);

		return $this->getLanguage()->getDynamicVariable('news.entry.commentResponse.notification.message', array(
			'news' => $news,
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);

		return $this->getLanguage()->getDynamicVariable('news.entry.commentResponse.notification.mail', array(
			'news' => $news,
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);

		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'object' => $news
		), '#comments');
	}
}
