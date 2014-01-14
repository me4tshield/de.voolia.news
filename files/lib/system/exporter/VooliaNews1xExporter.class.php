<?php
namespace news\system\exporter;
use wcf\data\like\Like;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Exports news from voolia news system
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class VooliaNews1xExporter extends AbstractExporter {
	/**
	 * wcf installation number
	 * @var	integer
	 */
	protected $dbNo = 1;

	/**
	 * @see	\wcf\system\exporter\AbstractExporter::$limits
	 */
	protected $limits = array(
		'de.voolia.news.entry' => 10,
		'de.voolia.news.entry.update' => 10,
		'de.voolia.news.category' => 20,
		'de.voolia.news.comment' => 10,
		'de.voolia.news.comment.response' => 10,
		'de.voolia.news.attachment' => 10
	);

	/**
	 * @see	\wcf\system\exporter\AbstractExporter::$methods
	 */
	protected $methods = array(
		'de.voolia.news.category' => 'NewsCategories',
		'de.voolia.news.entry' => 'NewsEntries',
		'de.voolia.news.entry.update' => 'NewsEntryUpdate',
		'de.voolia.news.comment' => 'NewsComments',
		'de.voolia.news.comment.response' => 'NewsCommentResponses',
		'de.voolia.news.attachment' => 'NewsAttachments',
		'de.voolia.news.like' => 'NewsLikes'
	);

	/**
	 * @see	\wcf\system\exporter\IExporter::validateDatabaseAccess()
	 */
	public function validateDatabaseAccess() {
		parent::validateDatabaseAccess();

		$sql = "SELECT	COUNT(*)
			FROM	news".$this->dbNo."_news";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
	}

	/**
	 * @see	\wcf\system\exporter\IExporter::validateFileAccess()
	 */
	public function validateFileAccess() {
		if (in_array('de.voolia.news.attachment', $this->selectedData)) {
			if (empty($this->fileSystemPath) || (!@file_exists($this->fileSystemPath . 'lib/core.functions.php') && !@file_exists($this->fileSystemPath . 'wcf/lib/core.functions.php'))) return false;
		}
		
		return true;
	}

	/**
	 * @see	\wcf\system\exporter\IExporter::init()
	 */
	public function init() {
		parent::init();

		if (preg_match('/^wcf(\d+)_$/', $this->databasePrefix, $match)) {
			$this->dbNo = $match[1];
		}

		// file system path
		if (!empty($this->fileSystemPath)) {
			if (!@file_exists($this->fileSystemPath . 'lib/core.functions.php') && @file_exists($this->fileSystemPath . 'wcf/lib/core.functions.php')) {
				$this->fileSystemPath = $this->fileSystemPath . 'wcf/';
			}
		}
	}

	/**
	 * @see	\wcf\system\exporter\IExporter::getSupportedData()
	 */
	public function getSupportedData() {
		return array(
			'de.voolia.news.entry' => array(
				'de.voolia.news.category',
				'de.voolia.news.entry.update',
				'de.voolia.news.comment',
				'de.voolia.news.comment.response',
				'de.voolia.news.attachment',
				'de.voolia.news.like'
			)
		);
	}

	/**
	 * @see	\wcf\system\exporter\IExporter::getQueue()
	 */
	public function getQueue() {
		$queue = array();

		if (in_array('de.voolia.news.entry', $this->selectedData)) {
			if (in_array('de.voolia.news.category', $this->selectedData)) $queue[] = 'de.voolia.news.category';
			$queue[] = 'de.voolia.news.entry';

			if (in_array('de.voolia.news.comment', $this->selectedData)) {
				$queue[] = 'de.voolia.news.comment';
				$queue[] = 'de.voolia.news.comment.response';
			}
			if (in_array('de.voolia.news.entry.update', $this->selectedData)) $queue[] = 'de.voolia.news.entry.update';
			if (in_array('de.voolia.news.attachment', $this->selectedData)) $queue[] = 'de.voolia.news.attachment';
			if (in_array('de.voolia.news.like', $this->selectedData)) $queue[] = 'de.voolia.news.like';
		}

		return $queue;
	}

	/**
	 * @see	wcf\system\exporter\IExporter::getDefaultDatabasePrefix()
	 */
	public function getDefaultDatabasePrefix() {
		return 'wcf1_';
	}

	/**
	 * Counts news categories.
	 */
	public function countNewsCategories() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_category
			WHERE	objectTypeID = ?";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.category', 'de.voolia.news.category')));
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * Export the news categories.
	 */
	public function exportNewsCategories($offset, $limit) {
		$sql = "SELECT		*
			FROM		wcf".$this->dbNo."_category
			WHERE		objectTypeID = ?
			ORDER BY	categoryID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.category', 'de.voolia.news.category')));
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.category')->import($row['categoryID'], array(
				'parentCategoryID' => $row['parentCategoryID'],
				'title' => $row['title'],
				'description' => $row['description'],
				'time' => $row['time'],
				'showOrder' => $row['showOrder'],
				'isDisabled' => $row['isDisabled']
			));
		}
	}

	/**
	 * Counts the news entries.
	 */
	public function countNewsEntries() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	news".$this->dbNo."_news";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * Export the news entries.
	 */
	public function exportNewsEntries($offset, $limit) {
		$newsIDs = array();
		$sql = "SELECT		*
			FROM		news".$this->dbNo."_news
			ORDER BY	newsID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			$newsIDs[] = $row['newsID'];
		}

		// get the news categories
		$categories = array();
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('newsID IN (?)', array($newsIDs));
		
		$sql = "SELECT	* 
			FROM	news".WCF_N."_news_to_category
			".$conditionBuilder;
		$statement = $this->database->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			if (!isset($categories[$row['newsID']])) $categories[$row['newsID']] = array();
			$categories[$row['newsID']][] = $row['categoryID'];
		}

		// get news tags
		$tags = $this->getTags('de.voolia.news.entry', $newsIDs);

		// get the news
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('news.newsID IN (?)', array($newsIDs));

		$sql = "SELECT		news.*, language.languageCode
			FROM		news".$this->dbNo."_news news
			LEFT JOIN	wcf".$this->dbNo."_language language
			ON		(news.languageID = language.languageID)
			".$conditionBuilder;
		$statement = $this->database->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			$additionalData = array();
			// language code
			if ($row['languageCode']) $additionalData['languageCode'] = $row['languageCode'];

			// categories
			if (isset($categories[$row['newsID']])) $additionalData['categories'] = $categories[$row['newsID']];

			// tags
			if (isset($tags[$row['newsID']])) $additionalData['tags'] = $tags[$row['newsID']];

			ImportHandler::getInstance()->getImporter('de.voolia.news.entry')->import($row['newsID'], array(
				'subject' => $row['subject'],
				'teaser' => $row['teaser'],
				'text' => $row['text'],
				'time' => $row['time'],
				'publicationDate' => $row['publicationDate'],
				'userID' => ($row['userID'] ?: null),
				'username' => $row['username'],
				'isHot' => $row['isHot'],
				'isAnnouncement' => $row['isAnnouncement'],
				'isCommentable' => $row['isCommentable'],
				'isArchived' => $row['isArchived'],
				'archivingDate' => $row['archivingDate'],
				'isPublished' => $row['isPublished'],
				'isDeleted' => $row['isDeleted'],
				'isActive' => $row['isActive'],
				'deleteTime' => $row['deleteTime'],
				'deleteReason' => $row['deleteReason'],
				'enableHtml' => $row['enableHtml'],
				'enableBBCodes' => $row['enableBBCodes'],
				'enableSmilies' => $row['enableSmilies'],
				'cumulativeLikes' => $row['cumulativeLikes'],
				'newsUpdates' => $row['newsUpdates'],
				'attachments' => $row['attachments'],
				'pictureID' => $row['pictureID'],
				'views' => $row['views'],
				'comments' => $row['comments'],
				'editTime' => $row['editTime'],
				'editCount' => $row['editCount'],
				'editReason' => $row['editReason'],
				'editUser' => $row['editUser'],
				'editNoteSuppress' => $row['editNoteSuppress']
			), $additionalData);
		}
	}

	/**
	 * Counts the news updates.
	 */
	public function countNewsEntryUpdate() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	news".$this->dbNo."_news_update";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * Export the news updates.
	 */
	public function exportNewsEntryUpdate($offset, $limit) {
		$updateIDs = array();
		$sql = "SELECT		*
			FROM		news".$this->dbNo."_news_update
			ORDER BY	updateID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			$updateIDs[] = $row['updateID'];
		}

		// get the update
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('updateID IN (?)', array($updateIDs));

		$sql = "SELECT		*
			FROM		news".$this->dbNo."_news_update
			".$conditionBuilder;
		$statement = $this->database->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.entry.update')->import($row['updateID'], array(
				'newsID' => $row['newsID'],
				'time' => $row['time'],
				'userID' => ($row['userID'] ?: null),
				'username' => $row['username'],
				'subject' => $row['subject'],
				'text' => $row['text'],
				'enableHtml' => $row['enableHtml'],
				'enableBBCodes' => $row['enableBBCodes'],
				'enableSmilies' => $row['enableSmilies']
			));
		}
	}

	/**
	 * Counts the news comments.
	 */
	public function countNewsComments() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_comment
			WHERE	objectTypeID = ?";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.voolia.news.comment')));
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * Export the news comments.
	 */
	public function exportNewsComments($offset, $limit) {
		$sql = "SELECT		*
			FROM		wcf".$this->dbNo."_comment
			WHERE		objectTypeID = ?
			ORDER BY	commentID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.voolia.news.comment')));
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.comment')->import($row['commentID'], array(
				'objectID' => $row['objectID'],
				'time' => $row['time'],
				'message' => $row['message'],
				'userID' => $row['userID'],
				'username' => $row['username']
			));
		}
	}

	/**
	 * Counts the news comment responses.
	 */
	public function countNewsCommentResponses() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_comment_response
			WHERE	commentID IN (SELECT commentID FROM wcf".$this->dbNo."_comment WHERE objectTypeID = ?)";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.voolia.news.comment')));
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Exports the news comment responses.
	 */
	public function exportNewsCommentResponses($offset, $limit) {
		$sql = "SELECT		*
			FROM		wcf".$this->dbNo."_comment_response
			WHERE		commentID IN (SELECT commentID FROM wcf".$this->dbNo."_comment WHERE objectTypeID = ?)
			ORDER BY	responseID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.voolia.news.comment')));
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.comment.response')->import($row['responseID'], array(
				'commentID' => $row['commentID'],
				'time' => $row['time'],
				'userID' => $row['userID'],
				'username' => $row['username'],
				'message' => $row['message'],
			));
		}
	}

	/**
	 * Count the news likes.
	 */
	public function countNewsLikes() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_like
			WHERE	objectTypeID = ?";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.like.likeableObject', 'de.voolia.news.likeableNews')));
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Export the news likes.
	 */
	public function exportNewsLikes($offset, $limit) {
		$sql = "SELECT		*
			FROM		wcf".$this->dbNo."_like
			WHERE		objectTypeID = ?
			ORDER BY	likeID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.like.likeableObject', 'de.voolia.news.likeableNews')));
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.like')->import(0, array(
				'objectID' => $row['objectID'],
				'objectUserID' => $row['objectUserID'],
				'userID' => $row['userID'],
				'likeValue' => $row['likeValue'],
				'time' => $row['time']
			));
		}
	}

	/**
	 * Count the news attachments.
	 */
	public function countNewsAttachments() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_attachment
			WHERE	objectTypeID = ?
			AND	objectID IS NOT NULL";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.attachment.objectType', 'de.voolia.news.entry')));
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * Export the news attachments.
	 */
	public function exportNewsAttachments($offset, $limit) {
		$sql = "SELECT		*
			FROM		wcf".$this->dbNo."_attachment
			WHERE		objectTypeID = ?
			AND		objectID IS NOT NULL
			ORDER BY	attachmentID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($this->getObjectTypeID('com.woltlab.wcf.attachment.objectType', 'de.voolia.news.entry')));
		while ($row = $statement->fetchArray()) {
			$fileLocation = $this->fileSystemPath . 'attachments/' . substr($row['fileHash'], 0, 2) . '/' . $row['attachmentID'] . '-' . $row['fileHash'];
			ImportHandler::getInstance()->getImporter('de.voolia.news.attachment')->import($row['attachmentID'], array(
				'objectID' => $row['objectID'],
				'userID' => $row['userID'],
				'fileType' => $row['fileType'],
				'filename' => $row['filename'],
				'filesize' => $row['filesize'],
				'fileHash' => $row['fileHash'],
				'uploadTime' => $row['uploadTime'],
				'isImage' => $row['isImage'],
				'width' => $row['width'],
				'height' => $row['height'],
				'downloads' => $row['downloads'],
				'lastDownloadTime' => $row['lastDownloadTime'],
				'showOrder' => $row['showOrder']
			), array('fileLocation' => $fileLocation));
		}
	}

	/**
	 * Get the tags from the news.
	 */
	public function getTags($objectType, array $objectIDs) {
		$tags = array();
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('tag_to_object.objectID IN (?)', array($objectIDs));
		$conditionBuilder->add('tag_to_object.objectTypeID = ?', array($this->getObjectTypeID('com.woltlab.wcf.tagging.taggableObject', $objectType)));
		
		// get tags
		$sql = "SELECT		tag.tagID, tag.name,
					tag_to_object.tagID, tag_to_object.objectID
			FROM		wcf".$this->dbNo."_tag_to_object tag_to_object
			LEFT JOIN	wcf".$this->dbNo."_tag tag
			ON		(tag.tagID = tag_to_object.tagID)
			".$conditionBuilder;
		$statement = $this->database->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			if (!isset($tags[$row['objectID']])) $tags[$row['objectID']] = array();
			$tags[$row['objectID']][] = $row['name'];
		}
		
		return $tags;
	}

	/**
	 * Get the object type id by object type name.
	 */
	public function getObjectTypeID($definitionName, $objectTypeName) {
		$sql = "SELECT	objectTypeID
			FROM	wcf".$this->dbNo."_object_type
			WHERE	objectType = ?
			AND	definitionID = (
					SELECT	definitionID
					FROM	wcf".$this->dbNo."_object_type_definition
					WHERE	definitionName = ?
				)";
		$statement = $this->database->prepareStatement($sql, 1);
		$statement->execute(array($objectTypeName, $definitionName));
		$row = $statement->fetchArray();
		if ($row !== false) return $row['objectTypeID'];
		
		return null;
	}
}
