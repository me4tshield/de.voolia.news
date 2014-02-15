<?php
namespace news\system\user\activity\event;
use news\data\news\ViewableNewsList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Activity event for liked news entries.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class LikeableNewsUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @see	\wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$newsIDs = array();
		foreach ($events as $event) {
			$newsIDs[] = $event->objectID;
		}

		$newsList = new ViewableNewsList();
		$newsList->enableAttachmentLoading(false);
		$newsList->getConditionBuilder()->add("news.newsID IN (?)", array($newsIDs));
		$newsList->readObjects();
		$newsEntries = $newsList->getObjects();

		foreach ($events as $event) {
			if (isset($newsEntries[$event->objectID])) {
				$news = $newsEntries[$event->objectID];

				if (!$news->canRead()) {
					continue;
				}
				$event->setIsAccessible();

				$text = WCF::getLanguage()->getDynamicVariable('news.recentActivity.likedNews', array('news' => $news));
				$event->setTitle($text);
				if ($newsEntries[$event->objectID]->teaser && NEWS_DASHBOARD_ACTIVITY_SHOW_NEWS_TEASER) {
					$event->setDescription($newsEntries[$event->objectID]->teaser);
				} else {
					$event->setDescription($newsEntries[$event->objectID]->getExcerpt());
				}			} else {
				$event->setIsOrphaned();
			}
		}
	}
}
