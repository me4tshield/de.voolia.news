<?php
namespace news\data\news;
use news\data\news\picture\NewsPicture;
use news\data\news\update\ViewableNewsUpdateList;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\comment\CommentHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents a viewable news entry.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class ViewableNews extends DatabaseObjectDecorator {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'news\data\news\News';

	/**
	 * effective visit time
	 * @var	integer
	 */
	protected $effectiveVisitTime = null;

	/**
	 * news picture object
	 * @var	\news\data\news\picture\NewsPicture
	 */
	protected $newsPicture = null;

	/**
	 * viewable news update list
	 * @var	\news\data\news\update\ViewableNewsUpdateList
	 */
	protected $newsUpdateList = null;

	/**
	 * user profile object
	 * @var	\wcf\data\user\UserProfile
	 */
	protected $userProfile = null;

	/**
	 * Returns the news picture object.
	 * 
	 * @return	\news\data\news\picture\NewsPicture
	 */
	public function getNewsPicture() {
		if ($this->newsPicture === null) {
			$this->newsPicture = new NewsPicture(null, $this->getDecoratedObject()->data);
		}

		return $this->newsPicture;
	}

	/**
	 * Returns a list of all related news updates.
	 * 
	 * @return	\news\data\news\update\ViewableNewsUpdateList
	 */
	public function getNewsUpdateList() {
		if ($this->newsUpdateList === null) {
			$this->newsUpdateList = new ViewableNewsUpdateList();
			$this->newsUpdateList->getConditionBuilder()->add('news_update.newsID = ?', array($this->newsID));
			$this->newsUpdateList->readObjects();
		}

		return $this->newsUpdateList;
	}

	/**
	 * Returns the author profile object.
	 * 
	 * @return	\wcf\data\user\UserProfile
	 */
	public function getUserProfile() {
		if ($this->userProfile === null) {
			$userData = $this->getDecoratedObject()->data;

			if (isset($userData['avatarFileHash'])) {
				$userData['fileHash'] = $userData['avatarFileHash'];
				$userData['width'] = $userData['avatarWidth'];
				$userData['height'] = $userData['avatarHeight'];
			}

			$this->userProfile = new UserProfile(new User(null, $userData));
		}

		return $this->userProfile;
	}

	/**
	 * Returns the effective visit time.
	 * 
	 * @return	integer
	 */
	public function getVisitTime() {
		if ($this->effectiveVisitTime === null) {
			if (WCF::getUser()->userID) {
				$this->effectiveVisitTime = max($this->visitTime, VisitTracker::getInstance()->getVisitTime('de.voolia.news.entry'));
			} else {
				$this->effectiveVisitTime = max(VisitTracker::getInstance()->getObjectVisitTime('de.voolia.news.entry', $this->newsID), VisitTracker::getInstance()->getVisitTime('de.voolia.news.entry'));
			}

			if ($this->effectiveVisitTime === null) {
				$this->effectiveVisitTime = 0;
			}
		}

		return $this->effectiveVisitTime;
	}

	/**
	 * Returns true if the news entry is new for the active user.
	 * 
	 * @return	boolean
	 */
	public function isNew() {
		if ($this->time > $this->getVisitTime()) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the language of this entry.
	 * 
	 * @return	\wcf\data\language\Language
	 */
	public function getLanguage() {
		if ($this->languageID) return LanguageFactory::getInstance()->getLanguage($this->languageID);

		return null;
	}

	/**
	 * Returns the flag icon for the entry language.
	 * 
	 * @return	string
	 */
	public function getLanguageIcon() {
		return '<img src="'.$this->getLanguage()->getIconPath().'" alt="" title="'.$this->getLanguage().'" class="jsTooltip iconFlag" />';
	}

	/**
	 * Gets a specific news entry as viewable news entry.
	 * 
	 * @param	integer		$newsID
	 * @return	\wcf\data\news\ViewableNews
	 */
	public static function getViewableNews($newsID) {
		$list = new ViewableNewsList();
		$list->enableAttachmentLoading(false);
		$list->setObjectIDs(array($newsID));
		$list->readObjects();
		$objects = $list->getObjects();
		if (isset($objects[$newsID])) return $objects[$newsID];
		return null;
	}
}
