<?php
namespace news\system\menu\page;
use news\data\category\NewsCategory;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\menu\page\DefaultPageMenuItemProvider;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * News page menu item provider.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPageMenuItemProvider extends DefaultPageMenuItemProvider {
	/**
	 * number of unread news
	 * @var	integer
	 */
	protected $notifications = null;

	/**
	 * @see	wcf\system\menu\page\PageMenuItemProvider::getNotifications()
	 */
	public function getNotifications() {
		if ($this->notifications === null) {
			$this->notifications = 0;

			if (WCF::getUser()->userID) {
				// load the user storage data
				UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));

				// get the news ids
				$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), 'newsUnreadEntries');

				// cache does not exist or is outdated
				if ($data[WCF::getUser()->userID] === null) {
					$categoryIDs = NewsCategory::getAccessibleCategoryIDs();

					if (!empty($categoryIDs)) {
						$conditionBuilder = new PreparedStatementConditionBuilder();
						$conditionBuilder->add("news.newsID IN (SELECT newsID FROM news".WCF_N."_news_to_category WHERE categoryID IN (?))", array($categoryIDs));
						$conditionBuilder->add("news.time > ?", array(VisitTracker::getInstance()->getVisitTime('de.voolia.news.entry')));
						$conditionBuilder->add("news.isActive = 1 AND news.isDeleted = 0 AND news.isPublished = 1");
						$conditionBuilder->add("tracked_visit.visitTime IS NULL");

						// apply language filter
						if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
							$conditionBuilder->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(WCF::getUser()->getLanguageIDs()));
						}

						// count news
						$sql = "SELECT 		COUNT(*) AS count
							FROM 		news".WCF_N."_news news
							LEFT JOIN 	wcf".WCF_N."_tracked_visit tracked_visit
							ON 		(tracked_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.voolia.news.entry')." AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = ".WCF::getUser()->userID.")
							".$conditionBuilder;
						$statement = WCF::getDB()->prepareStatement($sql);
						$statement->execute($conditionBuilder->getParameters());
						$row = $statement->fetchArray();
						$this->notifications = $row['count'];
					}

					// update the user storage data
					UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'newsUnreadEntries', $this->notifications);
				} else {
					$this->notifications = $data[WCF::getUser()->userID];
				}
			}
		}

		return $this->notifications;
	}
}
