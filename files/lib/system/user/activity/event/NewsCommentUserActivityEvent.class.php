<?php
namespace news\system\user\activity\event;
use news\data\news\ViewableNewsList;
use wcf\data\comment\CommentList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCommentUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @see	\wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$comentIDs = array();
		foreach ($events as $event) {
			$comentIDs[] = $event->objectID;
		}

		$commentList = new CommentList();
		$commentList->getConditionBuilder()->add("comment.commentID IN (?)", array($comentIDs));
		$commentList->readObjects();
		$comments = $commentList->getObjects();

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

		foreach ($events as $event) {
			if (isset($comments[$event->objectID])) {
				$comment = $comments[$event->objectID];

				if (isset($news[$comment->objectID])) {
					$newsEntry = $news[$comment->objectID];

					if (!$newsEntry->canRead()) {
						continue;
					}
					$event->setIsAccessible();

					$text = WCF::getLanguage()->getDynamicVariable('news.recentActivity.newsComment', array('news' => $newsEntry));
					$event->setTitle($text);
					$event->setDescription($comment->getExcerpt());
					continue;
				}
			}

			$event->setIsOrphaned();
		}
	}
}
