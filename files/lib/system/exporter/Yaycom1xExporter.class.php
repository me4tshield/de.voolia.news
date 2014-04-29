<?php
namespace news\system\exporter;
use wcf\data\like\Like;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exporter\AbstractExporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Exports news from yaycom news system
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class Yaycom1xExporter extends AbstractExporter {
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
		'de.voolia.news.category' => 20,
		'de.voolia.news.comment' => 10
	);

	/**
	 * @see	\wcf\system\exporter\AbstractExporter::$methods
	 */
	protected $methods = array(
		'de.voolia.news.category' => 'NewsCategories',
		'de.voolia.news.entry' => 'NewsEntries',
		'de.voolia.news.comment' => 'NewsComments',
		'de.voolia.news.like' => 'NewsLikes'
	);

	/**
	 * @see	\wcf\system\exporter\IExporter::validateDatabaseAccess()
	 */
	public function validateDatabaseAccess() {
		parent::validateDatabaseAccess();

		$sql = "SELECT	COUNT(*)
			FROM	wcf".$this->dbNo."_news";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
	}

	/**
	 * @see	\wcf\system\exporter\IExporter::validateFileAccess()
	 */
	public function validateFileAccess() {
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
				'de.voolia.news.comment',
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

			if (in_array('de.voolia.news.comment', $this->selectedData)) $queue[] = 'de.voolia.news.comment';
			if (in_array('de.voolia.news.like', $this->selectedData)) $queue[] = 'de.voolia.news.like';
		}

		return $queue;
	}

	/**
	 * @see	wcf\system\exporter\IExporter::getDefaultDatabasePrefix()
	 */
	public function getDefaultDatabasePrefix() {
		return 'wbb1_1_';
	}

	/**
	 * Counts news categories.
	 */
	public function countNewsCategories() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_news_category";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * Export the news categories.
	 */
	public function exportNewsCategories($offset, $limit) {
		$sql = "SELECT		*
			FROM		wcf".$this->dbNo."_news_category
			ORDER BY	categoryID";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.category')->import($row['categoryID'], array(
				'title' => $row['categoryTitle'],
				'parentCategoryID' => 0,
				'time' => TIME_NOW,
				'isDisabled' => 0,
				'showOrder' => $row['categoryOrder']
			));
		}
	}

	/**
	 * Counts the news entries.
	 */
	public function countNewsEntries() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_news";
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
			FROM		wcf".$this->dbNo."_news
			ORDER BY	newsID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			$newsIDs[] = $row['newsID'];
		}

		// get the news
		$sql = "SELECT	*
			FROM	wcf".$this->dbNo."_news";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			$additionalData = array();

			// categories
			$additionalData['categories'][] = $row['categoryID'];

			ImportHandler::getInstance()->getImporter('de.voolia.news.entry')->import($row['newsID'], array(
				'subject' => $row['subject'],
				'text' => $row['message'],
				'time' => $row['time'],
				'userID' => ($row['userID'] ?: null),
				'username' => '',
				'languageID' => 1,
				'isActive' => 1,
				'teaser' => $row['teaser'],
				'views' => $row['views'],
				'comments' => $row['comments'],
				'editCount' => $row['editCount'],
				'editReason' => $row['editReason'],
				'editUser' => $row['editUsername'],
				'enableHtml' => 1
			), $additionalData);
		}
	}

	/**
	 * Counts comments.
	 */
	public function countNewsComments() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_news_comment";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		return $row['count'];
	}

	/**
	 * Export comments.
	 */
	public function exportNewsComments($offset, $limit) {
		$sql = "SELECT		*
			FROM		wcf".$this->dbNo."_news_comment
			ORDER BY	commentID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.comment')->import($row['commentID'], array(
				'objectID' => $row['newsID'],
				'userID' => $row['userID'],
				'username' => $row['username'],
				'message' => $row['comment'],
				'time' => $row['time']
			));
		}
	}

	/**
	 * Count ratings for news likes.
	 */
	public function countNewsLikes() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_rating
			WHERE	objectName = ?
			AND	rating NOT IN (?, ?)
			AND	userID <> ?";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array('de.yaycom.news.entry', 0, 3, 0));
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Export ratings for news likes.
	 */
	public function exportNewsLikes($offset, $limit) {
		$sql = "SELECT		rating.*, news.newsID, news.userID AS objectUserID
			FROM		wcf".$this->dbNo."_rating rating
			LEFT JOIN	wcf".$this->dbNo."_news news
			ON		(news.newsID = rating.objectID)
			WHERE		rating.objectName = ?
			AND		rating.rating NOT IN (?, ?)
			AND		rating.userID <> ?
			ORDER BY	rating.objectID, rating.userID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array('de.yaycom.news.entry', 0, 3, 0));
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.voolia.news.like')->import(0, array(
				'objectID' => $row['objectID'],
				'userID' => $row['userID'],
				'objectUserID' => $row['objectUserID'],
				'likeValue' => ($row['rating'] > 3 ? Like::LIKE : Like::DISLIKE)
			));
		}
	}
}
