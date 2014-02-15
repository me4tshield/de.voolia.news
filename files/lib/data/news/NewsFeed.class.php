<?php
namespace news\data\news;
use wcf\data\IFeedEntry;
use wcf\system\request\LinkHandler;

/**
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsFeed extends ViewableNews implements IFeedEntry {
	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->getTitle();
	}

	/**
	 * @see	\wcf\data\ILinkableObject::getLink()
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'object' => $this->getDecoratedObject(),
			'appendSession' => false,
			'encodeTitle' => true
		));
	}

	/**
	 * @see	\wcf\data\IMessage::getExcerpt()
	 */
	public function getExcerpt($maxLength = 255) {
		return $this->getDecoratedObject()->getExcerpt($maxLength);
	}

	/**
	 * @see	\wcf\data\IMessage::getTime()
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @see	\wcf\data\IMessage::getUserID()
	 */
	public function getUserID() {
		return $this->userID;
	}

	/**
	 * @see	\wcf\data\IMessage::getUsername()
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @see	\wcf\data\IFeedEntry::getCategories()
	 */
	public function getCategories() {
		$categoryNames = array();
		foreach ($this->getDecoratedObject()->getCategories() as $category) {
			$categoryNames[] = $category->getTitle();
		}

		return $categoryNames;
	}

	/**
	 * @see	\wcf\data\IMessage::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return $this->getDecoratedObject()->getFormattedMessage();
	}

	/**
	 * @see	\wcf\data\IMessage::getMessage()
	 */
	public function getMessage() {
		return $this->getDecoratedObject()->getMessage();
	}

	/**
	 * @see	\wcf\data\IFeedEntry::getComments()
	 */
	public function getComments() {
		return $this->comments;
	}

	/**
	 * @see	\wcf\data\IMessage::__toString()
	 */
	public function __toString() {
		return $this->getDecoratedObject()->__toString();
	}

	/**
	 * @see	\wcf\data\IMessage::isVisible()
	 */
	public function isVisible() {
		return $this->canRead();
	}
}
