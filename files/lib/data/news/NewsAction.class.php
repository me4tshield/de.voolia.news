<?php
namespace news\data\news;
use news\data\news\source\NewsSourceAction;
use news\system\user\notification\object\NewsUserNotificationObject;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IMessageQuoteAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\like\LikeHandler;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\moderation\queue\ModerationQueueActivationManager;
use wcf\system\poll\PollManager;
use wcf\system\search\SearchIndexManager;
use wcf\system\tagging\TagEngine;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\user\activity\point\UserActivityPointHandler;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Executes the news entry actions.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsAction extends AbstractDatabaseObjectAction implements IMessageQuoteAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'news\data\news\NewsEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('user.news.canAddNews');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('mod.news.canEditNews');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('mod.news.canDeleteNews');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$resetCache
	 */
	protected $resetCache = array('create', 'delete', 'disable', 'enable', 'restore', 'trash', 'triggerPublication', 'update');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$allowGuestAccess
	 */
	protected $allowGuestAccess = array('getNewsPreview', 'quoteMessage', 'saveFullQuote', 'saveQuote', 'getMapMarkers');

	/**
	 * news editor object
	 * @var	\wcf\data\news\NewsEditor
	 */
	public $newsEditor = null;

	/**
	 * list of news data
	 * @var	array<array>
	 */
	public $newsData = array();

	/**
	 * viewable news object
	 * @var	\news\data\news\ViewableNews
	 */
	public $viewableNews = null;

	/**
	 * Adds news entry data.
	 *
	 * @param	\wcf\data\news\News	$news
	 * @param	string			$key
	 * @param	mixed			$value
	 */
	protected function addNewsData(News $news, $key, $value) {
		if (!isset($this->newsData[$news->newsID])) {
			$this->newsData[$news->newsID] = array();
		}

		$this->newsData[$news->newsID][$key] = $value;
	}

	/**
	 * Returns news data.
	 *
	 * @return	array<array>
	 */
	protected function getNewsData() {
		return array(
			'newsData' => $this->newsData
		);
	}

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		// count attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
		}

		$object = call_user_func(array($this->className, 'create'), $this->parameters['data']);
		$newsEditor = new NewsEditor($object);

		// add search index
		SearchIndexManager::getInstance()->add('de.voolia.news.entry', $object->newsID, $object->text, $object->subject, $object->time, $object->userID, $object->username, $object->languageID);

		// handle the news categories
		$newsEditor->updateCategoryIDs($this->parameters['categoryIDs']);
		$newsEditor->setCategoryIDs($this->parameters['categoryIDs']);

		// handle language id
		$languageID = (!isset($this->parameters['data']['languageID']) || ($this->parameters['data']['languageID'] === null)) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID'];

		// save the news tags
		if (!empty($this->parameters['tags'])) {
			TagEngine::getInstance()->addObjectTags('de.voolia.news.entry', $object->newsID, $this->parameters['tags'], $languageID);
		}

		// sources
		if (isset($this->parameters['sources'])) {
			$this->addSources($object->newsID, $this->parameters['sources']);
		}

		// update attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['attachmentHandler']->updateObjectID($object->newsID);
		}

		// news publication
		if (!$object->isDisabled && $object->isPublished) {
			$action = new NewsAction(array($newsEditor), 'triggerPublication');
			$action->executeAction();
		}

		return $object;
	}

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		// count attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
		}

		parent::update();

		// get ids
		$objectIDs = array();
		foreach ($this->objects as $news) {
			$objectIDs[] = $news->newsID;
		}

		if (!empty($objectIDs)) {
			// delete old search index entries
			SearchIndexManager::getInstance()->delete('de.voolia.news.entry', $objectIDs);

			// delete old sources if needed
			if (isset($this->parameters['sources'])) {
				$conditions = new PreparedStatementConditionBuilder();
				$conditions->add('newsID IN (?)', array($objectIDs));

				$sql = 'DELETE FROM	news'.WCF_N.'_news_source
					'.$conditions;
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditions->getParameters());
			}
		}

		foreach ($this->objects as $news) {
			// handle the news categories
			if (isset($this->parameters['categoryIDs'])) {
				$news->updateCategoryIDs($this->parameters['categoryIDs']);
			}

			// update the news tags
			if (isset($this->parameters['tags']) && !empty($this->parameters['tags'])) {
				// set language id (cannot be zero)
				$languageID = (!isset($this->parameters['data']['languageID']) || ($this->parameters['data']['languageID'] === null)) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID'];

				TagEngine::getInstance()->addObjectTags('de.voolia.news.entry', $news->newsID, $this->parameters['tags'], $languageID);
			}

			// update the news sources
			if (isset($this->parameters['sources'])) {
				$this->addSources($news->newsID, $this->parameters['sources']);
			}

			// create new search index entry
			SearchIndexManager::getInstance()->add('de.voolia.news.entry', $news->newsID, (isset($this->parameters['data']['text']) ? $this->parameters['data']['text'] : $news->text), (isset($this->parameters['data']['subject']) ? $this->parameters['data']['subject'] : $news->subject), $news->time, $news->userID, $news->username, $news->languageID);
		}

		// reset the user storage data
		UserStorageHandler::getInstance()->resetAll('newsUnreadEntries');
	}

	/**
	 * Validates the enable action.
	 */
	public function validateEnable() {
		if (empty($this->objects)) {
			$this->readObjects();
			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if ($news->isActive) {
				throw new UserInputException('objectIDs');
			}

			if (!$news->canActivateNews()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Enables news entries.
	 */
	public function enable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			$news->update(array(
				'isActive' => 1
			));
			$this->addNewsData($news->getDecoratedObject(), 'isActive', 1);

			// remove moderated content
			$this->removeModeratedContent($newsIDs);
		}

		// trigger publication
		if (!empty($newsIDs)) {
			$action = new NewsAction($newsIDs, 'triggerPublication');
			$action->executeAction();
		}

		$this->unmarkEntries();

		return $this->getNewsData();
	}

	/**
	 * Validates the disable action.
	 */
	public function validateDisable() {
		// read objects
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if ($news->isDisabled) {
				throw new UserInputException('objectIDs');
			}

			if (!$news->canDeactivateNews()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Disables news entries.
	 */
	public function disable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		$newsIDs = $perUserCount = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			$news->update(array(
				'isActive' => 0
			));
			$this->addNewsData($news->getDecoratedObject(), 'isActive', 0);

			if (!isset($perUserCount[$news->userID])) {
				$perUserCount[$news->userID] = 0;
			}
			$perUserCount[$news->userID]++;

			// add moderated content
			ModerationQueueActivationManager::getInstance()->addModeratedContent('de.voolia.news.entry', $news->newsID);
		}

		// remove the user activity
		UserActivityEventHandler::getInstance()->removeEvents('de.voolia.news.recentActivityEvent.news', $newsIDs);
		UserActivityPointHandler::getInstance()->removeEvents('de.voolia.news.activityPointEvent.news', $perUserCount);

		// reset the user storage data
		UserStorageHandler::getInstance()->resetAll('newsUnreadEntries');

		$removeItems = array();
		foreach ($perUserCount as $userID => $items) {
			$removeItems[$userID] = $items * -1;
		}
		NewsEditor::updateNewsCounter($removeItems);

		$this->unmarkEntries();

		return $this->getNewsData();
	}

	/**
	 * Publishes news entries.
	 */
	public function publish() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			$news->update(array(
				'isPublished' => 1,
				'time' => $news->publicationDate,
				'publicationDate' => 0
			));
		}

		// trigger publication
		if (!empty($newsIDs)) {
			$action = new NewsAction($newsIDs, 'triggerPublication');
			$action->executeAction();
		}
	}

	/**
	 * Triggers the publication of news entries.
	 */
	public function triggerPublication() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $news) {
			// fire user activity event
			UserActivityEventHandler::getInstance()->fireEvent('de.voolia.news.recentActivityEvent.news', $news->newsID, $news->languageID, $news->userID);
			UserActivityPointHandler::getInstance()->fireEvent('de.voolia.news.activityPointEvent.news', $news->newsID, $news->userID);

			// update the watched news objects
			$notificationObject = new NewsUserNotificationObject($news->getDecoratedObject());
			foreach ($news->getCategoryIDs() as $categoryID) {
				UserObjectWatchHandler::getInstance()->updateObject('de.voolia.news.category', $categoryID, 'news', 'de.voolia.news.entry', $notificationObject);
			}

			// updates the news counter
			NewsEditor::updateNewsCounter(array($news->userID => 1));
		}

		// reset the user storage data
		UserStorageHandler::getInstance()->resetAll('newsUnreadEntries');
	}

	/**
	 * Archives news entries.
	 */
	public function archive() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $news) {
			$news->update(array(
				'isArchived' => 1,
				'archivingDate' => 0
			));
		}
	}

	/**
	 * Validates parameters to trash news entries.
	 */
	public function validateTrash() {
		$this->readString('reason', true, 'data');

		// read objects
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if (!$news->isDeletable()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Trashes news entries.
	 */
	public function trash() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $news) {
			if ($news->isDeleted) {
				continue;
			}

			$news->update(array(
				'isDeleted' => 1,
				'deleteTime' => TIME_NOW,
				'deleteReason' => ((isset($this->parameters['data']['reason'])) ? $this->parameters['data']['reason'] : '')
			));
		}

		// reset the user storage data
		UserStorageHandler::getInstance()->resetAll('newsUnreadEntries');
		UserStorageHandler::getInstance()->resetAll('newsUnreadWatchedEntries');

		$this->unmarkEntries();
	}

	/**
	 * Validates parameters to restore news entries.
	 */
	public function validateRestore() {
		// read objects
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $entry) {
			if (!$entry->canRestoreNews()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Restores news entries.
	 */
	public function restore() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $news) {
			if (!$news->isDeleted) {
				continue;
			}

			$news->update(array(
				'isDeleted' => 0,
				'deleteTime' => 0,
				'deleteReason' => ""
			));
		}

		// reset the user storage data
		UserStorageHandler::getInstance()->resetAll('newsUnreadEntries');
		UserStorageHandler::getInstance()->resetAll('newsUnreadWatchedEntries');

		$this->unmarkEntries();
	}

	/**
	 * @see	\wcf\data\IDeleteAction::validateDelete()
	 */
	public function validateDelete() {
		// read objects
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if (!$news->isDeletable()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * @see	\wcf\data\IDeleteAction::delete()
	 */
	public function delete() {
		// delete news entries
		parent::delete();

		// collect data
		$newsIDs = $perUserCount = $pollIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;

			if ($news->pollID) {
				$pollIDs[] = $news->pollID;
			}

			if (!$news->isDisabled) {
				if (!isset($perUserCount[$news->userID])) {
					$perUserCount[$news->userID] = 0;
				}
				$perUserCount[$news->userID]--;
			}
		}

		if (!empty($newsIDs)) {
			// delete like data
			LikeHandler::getInstance()->removeLikes('de.voolia.news.likeableNews', $newsIDs);

			// delete comments
			CommentHandler::getInstance()->deleteObjects('de.voolia.news.comment', $newsIDs);

			// delete tag to object entries
			TagEngine::getInstance()->deleteObjects('de.voolia.news.entry', $newsIDs);

			// delete entry activity events
			UserActivityEventHandler::getInstance()->removeEvents('de.voolia.news.recentActivityEvent.news', $newsIDs);
			UserActivityPointHandler::getInstance()->removeEvents('de.voolia.news.activityPointEvent.news', $perUserCount);

			// delete entry from search index
			SearchIndexManager::getInstance()->delete('de.voolia.news.entry', $newsIDs);

			// remove object from moderation queue
			ModerationQueueActivationManager::getInstance()->removeModeratedContent('de.voolia.news.entry', $newsIDs);
		}

		// delete a poll
		if (!empty($pollIDs)) {
			PollManager::getInstance()->removePolls($pollIDs);
		}

		// reset the user storage data
		UserStorageHandler::getInstance()->resetAll('newsUnreadEntries');
		UserStorageHandler::getInstance()->resetAll('newsUnreadWatchedEntries');

		// remove news counter
		if (!empty($perUserCount)) {
			NewsEditor::updateNewsCounter($perUserCount);
		}

		$this->unmarkEntries();
	}

	/**
	 * Adds sources to the given news.
	 * 
	 * @param	integer		$newsID
	 * @param	array		$sources
	 */
	protected function addSources($newsID, $sources) {
		foreach ($sources as $source) {
			$objectAction = new NewsSourceAction(array(), 'create', array(
				'data' => array_merge($source, array('newsID' => $newsID))
			));
			$objectAction->executeAction();
		}
	}

	/**
	 * Validates the "getNewsPreview" action.
	 */
	public function validateGetNewsPreview() {
		$this->viewableNews = ViewableNews::getViewableNews(reset($this->objectIDs));

		if ($this->viewableNews === null || !$this->viewableNews->canRead()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * Gets a preview of a news entry.
	 * 
	 * @return	array
	 */
	public function getNewsPreview() {
		WCF::getTPL()->assign(array(
			'news' => $this->viewableNews
		));

		return array(
			'template' => WCF::getTPL()->fetch('newsPreview', 'news')
		);
	}
	
	/**
 	* Validates the setAsHot action for news.
 	*/
	public function validateSetAsHot() {
		if (empty($this->objects)) {
			$this->readObjects();
			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if ($news->isHot) {
				throw new UserInputException('objectIDs');
			}

			if (!$news->canSetNewsAsHot()) {
			throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Set news as hot.
	 */
	public function setAsHot() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $news) {
			$news->update(array(
					'isHot' => 1
			));
			$this->addNewsData($news->getDecoratedObject(), 'isHot', 1);
		}

		return $this->getNewsData();
	}

	/**
	 * Validates the unsetAsHot action for news.
	 */
	public function validateUnsetAsHot() {
		if (empty($this->objects)) {
			$this->readObjects();
			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if (!$news->isHot) {
				throw new UserInputException('objectIDs');
			}

			if (!$news->canSetNewsAsHot()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Unset news as hot.
	 */
	public function unsetAsHot() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $news) {
			$news->update(array(
					'isHot' => 0
			));
			$this->addNewsData($news->getDecoratedObject(), 'isHot', 0);
		}

		return $this->getNewsData();
	}

	/**
	 * Validates the activate action for news comments.
	 */
	public function validateActivateComments() {
		if (empty($this->objects)) {
			$this->readObjects();
			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if ($news->isCommentable) {
				throw new UserInputException('objectIDs');
			}

			if (!$news->canManageComments()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Activate news comments.
	 */
	public function activateComments() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			$news->update(array(
				'isCommentable' => 1
			));
			$this->addNewsData($news->getDecoratedObject(), 'isCommentable', 1);
		}

		return $this->getNewsData();
	}

	/**
	 * Validates the deactivate action for news comments.
	 */
	public function validateDeactivateComments() {
		// read objects
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}

		foreach ($this->objects as $news) {
			if (!$news->isCommentable) {
				throw new UserInputException('objectIDs');
			}

			if (!$news->canManageComments()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Deactivate news comments.
	 */
	public function deactivateComments() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			$news->update(array(
				'isCommentable' => 0
			));
			$this->addNewsData($news->getDecoratedObject(), 'isCommentable', 0);
		}
		return $this->getNewsData();
	}

	/**
	 * Marks a news as read.
	 */
	public function markAsRead() {
		if (empty($this->parameters['visitTime'])) {
			$this->parameters['visitTime'] = TIME_NOW;
		}

		if (empty($this->objects)) {
			$this->readObjects();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			VisitTracker::getInstance()->trackObjectVisit('de.voolia.news.entry', $news->newsID, $this->parameters['visitTime']);
		}

		// reset the user storage data
		if (WCF::getUser()->userID) {
			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'newsUnreadEntries');
			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'newsUnreadWatchedEntries');

			// delete obsolete user notifications
			if (!empty($newsIDs)) {
				UserNotificationHandler::getInstance()->deleteNotifications('news', 'de.voolia.news.entry', array(WCF::getUser()->userID), $newsIDs);
			}
		}
	}

	/**
	 * @see	\wcf\data\IMessageQuoteAction::validateSaveFullQuote()
	 */
	public function validateSaveFullQuote() {
		$this->newsEditor = $this->getSingleObject();

		// validate permissions
		if (!$this->newsEditor->canRead()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\data\IMessageQuoteAction::saveFullQuote()
	 */
	public function saveFullQuote() {
		if (!MessageQuoteManager::getInstance()->addQuote('de.voolia.news.entry', 0, $this->newsEditor->newsID, $this->newsEditor->getExcerpt(), $this->newsEditor->getMessage())) {
			$quoteID = MessageQuoteManager::getInstance()->getQuoteID('de.voolia.news.entry', $this->newsEditor->newsID, $this->newsEditor->getExcerpt(), $this->newsEditor->getMessage());
			MessageQuoteManager::getInstance()->removeQuote($quoteID);
		}

		return array(
			'count' => MessageQuoteManager::getInstance()->countQuotes(),
			'fullQuoteMessageIDs' => MessageQuoteManager::getInstance()->getFullQuoteObjectIDs(array('de.voolia.news.entry'))
		);
	}

	/**
	 * @see	\wcf\data\IMessageQuoteAction::validateSaveQuote()
	 */
	public function validateSaveQuote() {
		$this->readString('message');
		$this->newsEditor = $this->getSingleObject();

		if (!$this->newsEditor->canRead()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\data\IMessageQuoteAction::saveQuote()
	 */
	public function saveQuote() {
		MessageQuoteManager::getInstance()->addQuote('de.voolia.news.entry', 0, $this->newsEditor->newsID, $this->parameters['message']);

		return array(
			'count' => MessageQuoteManager::getInstance()->countQuotes(),
			'fullQuoteMessageIDs' => MessageQuoteManager::getInstance()->getFullQuoteObjectIDs(array('de.voolia.news.entry'))
		);
	}

	/**
	 * @see	\wcf\data\IMessageQuoteAction::validateGetRenderedQuotes()
	 */
	public function validateGetRenderedQuotes() { /* does nothing */ }

	/**
	 * @see	\wcf\data\IMessageQuoteAction::getRenderedQuotes()
	 */
	public function getRenderedQuotes() {
		$quotes = MessageQuoteManager::getInstance()->getQuotesByParentObjectID('de.voolia.news.entry', $this->newsEditor->newsID);

		return array(
			'template' => implode("\n\n", $quotes)
		);
	}

	/**
	 * Removes moderated content entries for given entry ids.
	 * 
	 * @param	array<integer>		$entryIDs
	 */
	protected function removeModeratedContent(array $newsIDs) {
		ModerationQueueActivationManager::getInstance()->removeModeratedContent('de.voolia.news.entry', $newsIDs);
	}

	/**
	 * Unmark news entries.
	 * 
	 * @param	array<integer>		$newsIDs
	 */
	protected function unmarkEntries(array $newsIDs = array()) {
		if (empty($newsIDs)) {
			foreach ($this->objects as $news) {
				$newsIDs[] = $news->newsID;
			}
		}
		
		if (!empty($newsIDs)) {
			ClipboardHandler::getInstance()->unmark($newsIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.voolia.news.entry'));
		}
	}

	/**
	 * Validate the 'getMapMarkers' action.
	 */
	public function validateGetMapMarkers() {
		/** nothing to do here **/
	}
	
	/**
	 * Get all news with location data for the map view.
	 */
	public function getMapMarkers() {
		$markers = array();

		// get all accessible news with location data
		$newsList = new AccessibleNewsList();
		$newsList->getConditionBuilder()->add('news.latitude <> ?', array(0));
		$newsList->getConditionBuilder()->add('news.longitude <> ?', array(0));
		$newsList->readObjects();
		$news = $newsList->getObjects();

		// show goole maps info window for every news entry
		foreach ($news as $entries) {
			$markers[] = array(
				'infoWindow' => WCF::getTPL()->fetch('mapEntryDialog', 'news', array(
					'news' => $entries
				)),
				'latitude' => $entries->latitude,
				'longitude' => $entries->longitude,
				'objectID' => $entries->newsID
			);
		}

		return array(
				'markers' => $markers
		);
	}}
