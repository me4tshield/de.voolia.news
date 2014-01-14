<?php
namespace news\data\news;
use news\data\category\NewsCategory;
use news\data\news\source\NewsSourceList;
use news\data\NewsDatabaseObject;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\data\category\Category;
use wcf\data\poll\Poll;
use wcf\data\IMessage;
use wcf\data\IPollObject;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\bbcode\MessageParser;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\category\CategoryHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\news\NewsPermissionHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\util\StringUtil;
use wcf\system\WCF;

/**
 * Represents a news entry.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class News extends NewsDatabaseObject implements IBreadcrumbProvider, IMessage, IPollObject, IRouteController {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'news';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'newsID';

	/**
	 * list of categories
	 * @var	array<\news\data\category\NewsCategory>
	 */
	protected $categories = null;

	/**
	 * list of category ids
	 * @var	array<integer>
	 */
	protected $categoryIDs = array();

	/**
	 * poll object for news entries
	 * @var	\wcf\data\poll\Poll
	 */
	protected $poll = null;

	/**
	 * Returns true if the active user has the permissions to read this news.
	 * 
	 * @return	boolean
	 */
	public function canRead() {
		if (!WCF::getSession()->getPermission('user.news.canViewNews')) {
			return false;
		}

		if (!$this->isActive) {
			if (!WCF::getSession()->getPermission('mod.news.canReadDeactivatedNews')) {
				return false;
			}
		}

		if ($this->isDeleted) {
			if (!WCF::getSession()->getPermission('mod.news.canReadDeletedNews')) {
				return false;
			}
		}

		if (!$this->isPublished) {
			if (WCF::getUser()->userID != $this->userID && !WCF::getSession()->getPermission('mod.news.canReadFutureNews')) {
				return false;
			}
		}

		foreach ($this->getCategories() as $category) {
			if ($category->isAccessible()) return true;
		}

		return false;
	}

	/**
	 * @see	\wcf\system\request\IRouteController::getID()
	 */
	public function getID() {
		return $this->newsID;
	}

	/**
	 * @see	\wcf\system\request\IRouteController::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see	\wcf\system\request\IRouteController::getTitle()
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @see	\wcf\data\IMessage::getMessage()
	 */
	public function getMessage() {
		return $this->text;
	}

	/**
	 * @see	\wcf\data\IMessage::getUserID()
	 */
	public function getUserID() {
		return $this->userID;
	}

	/**
	 *
	 * @see	\wcf\data\IUserContent::getUsername()
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 *
	 * @see	\wcf\data\ILinkableObject::getLink()
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('News', array(
				'application' => 'news',
				'object' => $this 
		));
	}

	/**
	 * @see	\wcf\system\breadcrumb\IBreadcrumbProvider::getBreadcrumb()
	 */
	public function getBreadcrumb() {
		return new Breadcrumb($this->subject, $this->getLink());
	}

	/**
	 * @see	\wcf\data\IMessage::__toString()
	 */
	public function __toString() {
		return $this->getFormattedMessage();
	}

	/**
	 * @see	\wcf\data\IMessage::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		// assign embedded attachments
		AttachmentBBCode::setObjectID($this->newsID);

		// parse and return the news message
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}

	/**
	 * @see	\wcf\data\IMessage::getFormattedMessage()
	 */
	public function getFormattedNewsUpdate() {
		// parse and return the news message
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->newsUpdate, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}

	/**
	 * @see	\wcf\data\IMessage::getExcerpt()
	 */
	public function getExcerpt($maxLength = 255) {
		MessageParser::getInstance()->setOutputType('text/simplified-html');
		$message = MessageParser::getInstance()->parse($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);

		return StringUtil::truncateHTML($message, $maxLength);
	}

	/**
	 * Returns the sources of this news.
	 *
	 * @return	array<\news\data\news\source\NewsSource>
	 */
	public function getSources() {
		if ($this->sources === null) {
			$sourceList = new NewsSourceList();
			$sourceList->getConditionBuilder()->add('news_source.newsID = ?', array($this->newsID));
			$sourceList->readObjects();
			$this->sources = $sourceList->getObjects();
		}

		return $this->sources;
	}

	/**
	 * Gets and assigns embedded attachments.
	 * 
	 * @return	\wcf\data\attachment\GroupedAttachmentList
	 */
	public function getAttachments() {
		if (MODULE_ATTACHMENT == 1 && $this->attachments) {
			$attachmentList = new GroupedAttachmentList('de.voolia.news.entry');
			$attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array($this->newsID));
			$attachmentList->readObjects();

			// set embedded attachments
			AttachmentBBCode::setAttachmentList($attachmentList);

			return $attachmentList;
		}

		return null;
	}

	/**
	 * Returns true, if the news is editable by the current user.
	 * 
	 * @return	boolean
	 */
	public function canManageNews() {
		if (WCF::getSession()->getPermission('mod.news.canEditNews') || WCF::getSession()->getPermission('mod.news.canDeleteNews') || WCF::getSession()->getPermission('mod.news.canDeactivateNews') || WCF::getSession()->getPermission('mod.news.canActivateNews') || ($this->userID == WCF::getUser()->userID)) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the news is editable by the current user.
	 * 
	 * @return	boolean
	 */
	public function isEditable() {
		if (WCF::getSession()->getPermission('mod.news.canEditNews') || (WCF::getSession()->getPermission('user.news.canEditOwnNews') && $this->userID == WCF::getUser()->userID)) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the news is deletable by the current user.
	 * 
	 * @return	boolean
	 */
	public function isDeletable() {
		if (WCF::getSession()->getPermission('mod.news.canDeleteNews')) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the current user can restore a news.
	 * 
	 * @return	boolean
	 */
	public function canRestoreNews() {
		if (WCF::getSession()->getPermission('mod.news.canRestoreNews')) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the current user can deactivate a news.
	 * 
	 * @return	boolean
	 */
	public function canDeactivateNews() {
		if (WCF::getSession()->getPermission('mod.news.canDeactivateNews')) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the current user can activate a news.
	 * 
	 * @return	boolean
	 */
	public function canActivateNews() {
		if (WCF::getSession()->getPermission('mod.news.canActivateNews')) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the current user can manage comments for this news.
	 * 
	 * @return	boolean
	 */
	public function canManageComments() {
		if (WCF::getSession()->getPermission('mod.news.canManageComments')) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the news is commentable.
	 * 
	 * @return	boolean
	 */
	public function isCommentable() {
		if (WCF::getSession()->getPermission('user.news.canSeeComments') && $this->isCommentable) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true, if the news "isHot".
	 * 
	 * @return	boolean
	 */
	public function isHot() {
		return $this->isHot;
	}

	/**
	 * Update the hits of a news entry
	 */
	public function updateVisits() {
        	$views = $this->views + 1;

		// update news views
        	$sql = "UPDATE	news".WCF_N."_news
             	   	SET	views = ".$views."
             	   	WHERE	newsID = ?";
             	$statement = WCF::getDB()->prepareStatement($sql);
             	$statement->execute(array($this->newsID));
        }

	/**
	 * Update the number of news updates
	 */
	public function updateNewsUpdates() {
        	$newsUpdates = $this->newsUpdates - 1;

		// update news updates
        	$sql = "UPDATE	news".WCF_N."_news
             	   	SET	newsUpdates = ".$newsUpdates."
             	   	WHERE	newsID = ?";
             	$statement = WCF::getDB()->prepareStatement($sql);
             	$statement->execute(array($this->newsID));
        }

	/**
	 * Returns a poll object for a news entry.
	 * 
	 * @return	\wcf\data\poll\Poll
	 */
	public function getPoll() {
		if ($this->pollID && !$this->poll) {
			// new poll object
			$this->poll = new Poll($this->pollID);

			if ($this->poll->pollID) {
				$this->poll->setRelatedObject($this);
			}
			else {
				$this->poll = null;
			}
		}
		
		return $this->poll;
	}
	
	/**
	 * @see	\wcf\data\IPollObject::canVote()
	 */
	public function canVote() {
		if (WCF::getSession()->getPermission('user.news.canVote')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns the list of category ids.
	 * 
	 * @return	array<integer>
	 */
	public function getCategoryIDs() {
		return $this->categoryIDs;
	}

	/**
	 * Sets a category id.
	 * 
	 * @param	integer		$categoryID
	 */
	public function setCategoryID($categoryID) {
		$this->categoryIDs[] = $categoryID;
	}

	/**
	 * Sets a category ids.
	 * 
	 * @param	array<integer>		$categoryIDs
	 */
	public function setCategoryIDs(array $categoryIDs) {
		$this->categoryIDs= $categoryIDs;
	}

	/**
	 * Returns the categories of this news entry. 
	 * 
	 * @return	array<\news\data\category\NewsCategory>
	 */
	public function getCategories() {
		if ($this->categories === null) {
			$this->categories = array();

			if (!empty($this->categoryIDs)) {
				foreach ($this->categoryIDs as $categoryID) {
					$this->categories[$categoryID] = new NewsCategory(CategoryHandler::getInstance()->getCategory($categoryID));
				}
			} else {
				$sql = "SELECT	categoryID
					FROM	news".WCF_N."_news_to_category
					WHERE	newsID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array($this->newsID));
				while ($row = $statement->fetchArray()) {
					$this->categories[$row['categoryID']] = new NewsCategory(CategoryHandler::getInstance()->getCategory($row['categoryID']));
				}
			}
		}

		return $this->categories;
	}

	/**
	 * @see	\wcf\data\IMessage::isVisible()
	 */
	public function isVisible() {
		return $this->canRead();
	}
}
