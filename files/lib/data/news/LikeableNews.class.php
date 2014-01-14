<?php
namespace news\data\news;
use wcf\data\like\object\AbstractLikeObject;
use wcf\system\request\LinkHandler;

/**
 * Implementation for likeable object.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class LikeableNews extends AbstractLikeObject {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'news\data\news\News';

	/**
	 * @see	\wcf\data\like\object\ILikeObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->newsID;
	}

	/**
	 * @see	\wcf\data\like\object\ILikeObject::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see	\wcf\data\like\object\ILikeObject::getURL()
	 */
	public function getURL() {
		return LinkHandler::getInstance()->getLink('News', array(
				'application' => 'news',
				'newsID' => $this->newsID 
		), '#news' . $this->newsID);
	}

	/**
	 * @see	\wcf\data\like\object\ILikeObject::getUserID()
	 */
	public function getUserID() {
		return $this->userID;
	}

	/**
	 * @see	\wcf\data\like\object\ILikeObject::updateLikeCounter()
	 */
	public function updateLikeCounter($cumulativeLikes) {
		// update cumulative likes
		$entryEditor = new NewsEditor($this->getDecoratedObject());
		$entryEditor->update(array(
			'cumulativeLikes' => $cumulativeLikes
		));
	}
}
