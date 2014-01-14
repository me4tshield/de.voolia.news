<?php
namespace news\system\worker;
use news\data\news\NewsEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\search\SearchIndexManager;
use wcf\system\user\activity\point\UserActivityPointHandler;
use wcf\system\worker\AbstractRebuildDataWorker;
use wcf\system\WCF;

/**
 * Worker actions for news entries.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsDataWorker extends AbstractRebuildDataWorker {
	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::$objectListClassName
	 */
	protected $objectListClassName = 'news\data\news\NewsList';

	/**
	 * @see	\wcf\system\worker\AbstractWorker::$limit
	 */
	protected $limit = 250;

	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::initObjectList
	 */
	protected function initObjectList() {
		parent::initObjectList();

		$this->objectList->sqlOrderBy = 'news.newsID';
	}

	/**
	 * @see	\wcf\system\worker\IWorker::execute()
	 */
	public function execute() {
		parent::execute();

		if (!count($this->objectList)) {
			return;
		}

		if (!$this->loopCount) {
			// remove the activity points
			UserActivityPointHandler::getInstance()->reset('de.voolia.news.activityPointEvent.news');

			// remove the entry from search index
			SearchIndexManager::getInstance()->reset('de.voolia.news.entry');
		}

		// get news attachments
		$attachmentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', 'de.voolia.news.entry');
		$sql = "SELECT		COUNT(*) AS attachments
			FROM		wcf".WCF_N."_attachment
			WHERE		objectTypeID = ?
			AND		objectID = ?";
		$attachments = WCF::getDB()->prepareStatement($sql);

		// calculate the cumulative likes
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("objectID IN (?)", array($this->objectList->getObjectIDs()));
		$conditions->add("objectTypeID = ?", array(ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.like.likeableObject', 'de.voolia.news.likeableNews')));

		$sql = "SELECT	objectID,
				cumulativeLikes
			FROM	wcf".WCF_N."_like_object
			".$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		$likes = array();
		while ($row = $statement->fetchArray()) {
			$likes[$row['objectID']] = $row['cumulativeLikes'];
		}

		// update the news entries
		$userItems = array();
		foreach ($this->objectList as $news) {
			// new EntryEditor
			$editor = new NewsEditor($news);

			// update search index
			SearchIndexManager::getInstance()->add('de.voolia.news.entry', $news->newsID, $news->message, $news->subject, $news->time, $news->userID, $news->username, $news->languageID);

			// news data
			$newsData = array();

			// likes
			$newsData['cumulativeLikes'] = (isset($likes[$news->newsID])) ? $likes[$news->newsID] : 0;

			// attachments
			$attachments->execute(array($attachmentObjectType->objectTypeID, $news->newsID));
			$row = $attachments->fetchArray();
			$newsData['attachments'] = $row['attachments'];

			if ($news->userID) {
				if (!isset($userItems[$news->userID])) {
					$userItems[$news->userID] = 0;
				}
				$userItems[$news->userID]++;
			}

			$editor->update($newsData);
		}

		// update activity points
		UserActivityPointHandler::getInstance()->fireEvents('de.voolia.news.activityPointEvent.news', $userItems, false);
	}
}
