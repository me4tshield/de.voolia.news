<?php
namespace news\system\poll;
use news\data\news\News;
use wcf\data\poll\Poll;
use wcf\system\poll\AbstractPollHandler;
use wcf\system\WCF;

/**
 * IPollHandler Implementation for a news entry.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPollHandler extends AbstractPollHandler {
	/**
	 * @see	\wcf\system\poll\AbstractPollHandler::canStartPublicPoll()
	 */
	public function canStartPublicPoll() {
		return true;
	}
	
	/**
	 * @see	\wcf\system\poll\IPollHandler::canVote()
	 */
	public function canVote() {
		if (WCF::getSession()->getPermission('user.news.canVote')) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @see	\wcf\system\poll\IPollHandler::getRelatedObject()
	 */
	public function getRelatedObject(Poll $poll) {
		$news = new News($poll->objectID);
		if ($news->newsID && $news->pollID == $poll->pollID) {
			return $news;
		}
		
		return null;
	}
}
