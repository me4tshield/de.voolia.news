<?php
namespace news\system\user\activity\event;
use news\data\news\ViewableNewsList;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\comment\CommentList;
use wcf\data\user\UserList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @see	\wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$responseIDs = array();
		foreach ($events as $event) {
			$responseIDs[] = $event->objectID;
		}

		$responseList = new CommentResponseList();
		$responseList->getConditionBuilder()->add("comment_response.responseID IN (?)", array($responseIDs));
		$responseList->readObjects();
		$responses = $responseList->getObjects();

		$commentIDs = $comments = array();
		foreach ($responses as $response) {
			$commentIDs[] = $response->commentID;
		}
		if (!empty($commentIDs)) {
			$commentList = new CommentList();
			$commentList->getConditionBuilder()->add("comment.commentID IN (?)", array($commentIDs));
			$commentList->readObjects();
			$comments = $commentList->getObjects();
		}

		// fetch news entries
		$newsIDs = $news = array();
		foreach ($comments as $comment) {
			$newsIDs[] = $comment->objectID;
		}
		if (!empty($newsIDs)) {
			$newsList = new ViewableNewsList();
			$newsList->enableAttachmentLoading(false);
			$newsList->getConditionBuilder()->add("news.newsID IN (?)", array($newsIDs));
			$newsList->readObjects();
			$news = $newsList->getObjects();
		}

		$userIDs = $user = array();
		foreach ($comments as $comment) {
			$userIDs[] = $comment->userID;
		}
		if (!empty($userIDs)) {
			$userList = new UserList();
			$userList->getConditionBuilder()->add("user_table.userID IN (?)", array($userIDs));
			$userList->readObjects();
			$users = $userList->getObjects();
		}

		foreach ($events as $event) {
			if (isset($responses[$event->objectID])) {
				$response = $responses[$event->objectID];
				$comment = $comments[$response->commentID];
				if (isset($news[$comment->objectID]) && isset($users[$comment->userID])) {
					$newsEntry = $news[$comment->objectID];

					if (!$newsEntry->canRead()) {
						continue;
					}
					$event->setIsAccessible();

					$text = WCF::getLanguage()->getDynamicVariable('news.recentActivity.newsCommentResponse', array(
						'commentAuthor' => $users[$comment->userID],
						'news' => $newsEntry
					));
					$event->setTitle($text);
					$event->setDescription($response->getExcerpt());
					continue;
				}
			}

			$event->setIsOrphaned();
		}
	}
}
