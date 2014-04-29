<?php
namespace news\data\news\update;
use news\data\news\ViewableNews;
use news\data\NewsDatabaseObject;
use wcf\data\IMessage;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\bbcode\MessageParser;
use wcf\system\request\LinkHandler;
use wcf\util\StringUtil;

/**
 * Represents a news update.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdate extends NewsDatabaseObject implements IMessage {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'news_update';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'updateID';

	/**
	 * @see	\wcf\data\IMessage::getExcerpt()
	 */
	public function getExcerpt($maxLength = 255) {
		MessageParser::getInstance()->setOutputType('text/simplified-html');
		$message = MessageParser::getInstance()->parse($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);

		return StringUtil::truncateHTML($message, $maxLength);
	}

	/**
	 * @see	\wcf\data\IMessage::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		// assign embedded attachments
		AttachmentBBCode::setObjectID($this->updateID);

		// parse and return the news message
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}

	/**
	 * @see	\wcf\data\ILinkableObject::getLink()
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'object' => $this->getNews(),
			'updateID' => $this->updateID
		));
	}

	/**
	 * @see	\wcf\data\IMessage::getMessage()
	 */
	public function getMessage() {
		return $this->text;
	}

	/**
	 * Returns the related viewable news object
	 * 
	 * @return	\news\data\news\ViewableNews
	 */
	public function getNews() {
		return ViewableNews::getViewableNews($this->newsID);
	}

	/**
	 * @see	\wcf\data\IUserContent::getTime()
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see  \wcf\data\IUserContent::getUserID()
	 */
	public function getUserID() {
		return $this->userID;
	}

	/**
	 * @see  \wcf\data\IUserContent::getUsername()
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @see  \wcf\data\IMessage::isVisible()
	 */
	public function isVisible() {
		return $this->getNews()->canRead();
	}

	/**
	 * @see	\wcf\data\IMessage::__toString()
	 */
	public function __toString() {
		return $this->getFormattedMessage();
	}
}
