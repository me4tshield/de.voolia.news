<?php
namespace news\system\user\notification\event;
use news\data\news\News;
use wcf\data\comment\Comment;
use wcf\data\user\User;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event for news comments.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCommentResponseOwnerUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('news.entry.commentResponseOwner.notification.title');
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);
		$commentAuthor = new User($comment->userID);

		return $this->getLanguage()->getDynamicVariable('news.entry.commentResponseOwner.notification.message', array(
			'news' => $news,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);
		$commentAuthor = new User($comment->userID);

		return $this->getLanguage()->getDynamicVariable('news.entry.commentResponseOwner.notification.mail', array(
			'news' => $news,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor
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
