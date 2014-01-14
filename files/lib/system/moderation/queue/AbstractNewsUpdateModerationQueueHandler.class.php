<?php
namespace news\system\moderation\queue;
use news\data\news\update\NewsUpdateList;
use news\data\news\update\NewsUpdateAction;
use news\data\news\update\NewsUpdate;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\system\moderation\queue\AbstractModerationQueueHandler;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;

/**
 * Implementation for news updates of IModerationQueueHandler
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
abstract class AbstractNewsUpdateModerationQueueHandler extends AbstractModerationQueueHandler {
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$className
	 */
	protected $className = 'news\data\news\update\NewsUpdate';

	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$objectType
	 */
	protected $objectType = 'de.voolia.news.entry.update';

	/**
	 * list of news objects
	 * @var	array<news\data\news\News>
	 */
	protected static $news = array();

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::assignQueues()
	 */
	public function assignQueues(array $queues) {
		$assignments = array();
		foreach ($queues as $queue) {
			$assignUser = false;
			if (WCF::getSession()->getPermission('mod.news.canModerateNews')) {
				$assignUser = true;
			}

			$assignments[$queue->queueID] = $assignUser;
		}

		ModerationQueueManager::getInstance()->setAssignment($assignments);
	}

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::getContainerID()
	 */
	public function getContainerID($objectID) {
		return 0;
	}

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::isValid()
	 */
	public function isValid($objectID) {
		if ($this->getViewableNewsUpdate($objectID) === null) {
			return false;
		}

		return true;
	}

	/**
	 * Returns a news object by news id
	 * 
	 * @param	integer		$objectID
	 * @return	news\data\news\News
	 */
	protected function getViewableNewsUpdate($objectID) {
		if (!array_key_exists($objectID, self::$news)) {
			self::$news[$objectID] = new NewsUpdate($objectID);
			if (!self::$news[$objectID]->updateID) {
				self::$news[$objectID] = null;
			}
		}

		return self::$news[$objectID];
	}

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::populate()
	 */
	public function populate(array $queues) {
		$objectIDs = array();
		foreach ($queues as $object) {
			$objectIDs[] = $object->objectID;
		}

		// fetch news entries
		$newsList = new NewsUpdateList();
		$newsList->setObjectIDs($objectIDs);
		$newsList->readObjects();
		$news = $newsList->getObjects();

		foreach ($queues as $object) {
			if (isset($news[$object->objectID])) {
				$object->setAffectedObject($news[$object->objectID]);
			} else {
				$object->setIsOrphaned();
			}
		}
	}

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::removeContent()
	 */
	public function removeContent(ModerationQueue $queue, $message) {
		if ($this->isValid($queue->objectID)) {
			// remove news from moderation center
			$action = new NewsUpdateAction(array($this->getViewableNewsUpdate($queue->objectID)), 'delete');
			$action->executeAction();
		}
	}
}
