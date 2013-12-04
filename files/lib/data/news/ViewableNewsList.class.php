<?php
namespace news\data\news;
use news\data\category\NewsCategory;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\like\LikeHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents a list of viewable news entries.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class ViewableNewsList extends NewsList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$decoratorClassName
	 */
	public $decoratorClassName = 'news\data\news\ViewableNews';

	/**
	 * attachment list
	 * @var	\wcf\data\attachment\GroupedAttachmentList
	 */
	protected $attachmentList = null;

	/**
	 * attachment object ids
	 * @var	array<integer>
	 */
	public $attachmentObjectIDs = array();

	/**
	 * enables/disable the loading of attachments
	 * @var	boolean
	 */
	protected $attachmentLoading = true;

	/**
	 * Creates a new ViewableNewsList object.
	 */
	public function __construct() {
		parent::__construct();

		// get avatars
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ', ';
		$this->sqlSelects .= "user_avatar.*, user_table.*";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = news.userID)";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user_avatar user_avatar ON (user_avatar.avatarID = user_table.avatarID)";

		// get news pictures
		$this->sqlSelects .= ", news_picture.categoryID, news_picture.fileHash, news_picture.fileExtension";
		$this->sqlJoins .= " LEFT JOIN news".WCF_N."_news_picture news_picture ON (news.pictureID = news_picture.pictureID)";

		// get the news like status
		$this->sqlSelects .= ", like_object.likes, like_object.dislikes";
		$this->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_like_object like_object ON (like_object.objectTypeID = " . LikeHandler::getInstance()->getObjectType('de.voolia.news.likeableNews')->objectTypeID . " AND like_object.objectID = news.newsID)";

		if (WCF::getUser()->userID != 0) {
			// last news visit time
			if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
			$this->sqlSelects .= 'tracked_visit.visitTime';
			$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.voolia.news.entry')." AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = ".WCF::getUser()->userID.")";
		}
	}

	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		parent::readObjects();

		// get attachments
		if ($this->attachmentLoading) {
			foreach ($this->objects as $news) {
				if ($news->attachments) {
					$this->attachmentObjectIDs[] = $news->newsID;
				}
			}
			$this->readAttachments();
		}
	}

	/**
	 * Gets a list of attachments.
	 */
	public function readAttachments() {
		if (MODULE_ATTACHMENT && !empty($this->attachmentObjectIDs)) {
			$this->attachmentList = new GroupedAttachmentList('de.voolia.news.entry');
			$this->attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array($this->attachmentObjectIDs));
			$this->attachmentList->readObjects();

			// set embedded attachments
			AttachmentBBCode::setAttachmentList($this->attachmentList);
		}
	}

	/**
	 * Enables/disable the loading of attachments.
	 * 
	 * @param	boolean		$enable
	 */
	public function enableAttachmentLoading($enable = true) {
		$this->attachmentLoading = $enable;
	}

	/**
	 * Returns the list of attachments
	 * 
	 * @return	\wcf\data\attachment\GroupedAttachmentList
	 */
	public function getAttachmentList() {
		return $this->attachmentList;
	}
}
