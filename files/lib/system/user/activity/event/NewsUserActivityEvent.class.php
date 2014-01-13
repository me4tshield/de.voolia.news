<?php
namespace news\system\user\activity\event;
use news\data\news\ViewableNewsList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Activity event for news.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @see	\wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$objectIDs = array();
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}

		$newsList = new ViewableNewsList();
		$newsList->getConditionBuilder()->add("news.newsID IN (?)", array($objectIDs));
		$newsList->readObjects();
		$newsEntries = $newsList->getObjects();

		foreach ($events as $event) {
			if (isset($newsEntries[$event->objectID])) {
				if (!$newsEntries[$event->objectID]->canRead()) {
					continue;
				}
				$event->setIsAccessible();

				$text = WCF::getLanguage()->getDynamicVariable('news.recentActivity.news', array('news' => $newsEntries[$event->objectID]));
				$event->setTitle($text);
				if ($newsEntries[$event->objectID]->teaser && NEWS_DASHBOARD_ACTIVITY_SHOW_NEWS_TEASER) {
					$event->setDescription($newsEntries[$event->objectID]->teaser);
				} else {
					$event->setDescription($newsEntries[$event->objectID]->getExcerpt());
				}
			} else {
				$event->setIsOrphaned();
			}
		}
	}
}
