<?php 
namespace news\data\news;
use news\data\category\NewsCategory;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Represents a list of accessible entries.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class AccessibleNewsList extends ViewableNewsList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$sqlOrderBy
	 */
	public $sqlOrderBy = 'news.time DESC';

	/**
	 * Creates the AccessibleNewsList object.
	 */
	public function __construct() {
		parent::__construct();

		// accessible news categories
		$accessibleCategoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if (!empty($accessibleCategoryIDs)) $this->getConditionBuilder()->add('news.newsID IN (SELECT newsID FROM news'.WCF_N.'_news_to_category WHERE categoryID IN (?))', array($accessibleCategoryIDs));
		else $this->getConditionBuilder()->add('1=0');

		// default conditions
		if (!WCF::getSession()->getPermission('mod.news.canReadDeactivatedNews')) $this->getConditionBuilder()->add('news.isActive = 1');
		if (!WCF::getSession()->getPermission('mod.news.canReadDeletedNews')) $this->getConditionBuilder()->add('news.isDeleted = 0');

		if (!WCF::getSession()->getPermission('mod.news.canReadFutureNews')) {
			if (WCF::getUser()->userID) $this->getConditionBuilder()->add('(news.isPublished = 1 OR news.userID = ?)', array(WCF::getUser()->userID));
			else $this->getConditionBuilder()->add('news.isPublished = 1');
		}

		// apply language filter
		if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
			$this->getConditionBuilder()->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(WCF::getUser()->getLanguageIDs()));
		}
	}

	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		if ($this->objectIDs === null) $this->readObjectIDs();
		parent::readObjects();
	}
}
