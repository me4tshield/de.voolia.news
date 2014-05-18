<?php
namespace news\system\cronjob;
use news\data\news\NewsAction;
use news\data\news\NewsList;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\WCF;

/**
 * Manages and archived news entries.
 * 
 * @author 	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class PublicationCronjob extends AbstractCronjob {
	/**
	 * @see	wcf\system\cronjob\ICronjob::execute()
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);

		// get delayed news
		$newsList = new NewsList();
		$newsList->getConditionBuilder()->add('news.isPublished = 0');
		$newsList->getConditionBuilder()->add('news.publicationDate <= ?', array(TIME_NOW));
		$newsList->readObjects();

		if (count($newsList->getObjects())) {
			// publish news
			$action = new NewsAction($newsList->getObjects(), 'publish');
			$action->executeAction();
		}

		// get outdated news
		$newsList = new NewsList();
		$newsList->getConditionBuilder()->add('news.isArchived = 0');
		$newsList->getConditionBuilder()->add('news.archivingDate <> 0 AND news.archivingDate <= ?', array(TIME_NOW));
		$newsList->readObjects();

		if (count($newsList->getObjects())) {
			// archivate news
			$action = new NewsAction($newsList->getObjects(), 'archive');
			$action->executeAction();
		}
		
		// get deleted news
		if (NEWS_ENABLE_EMPTY_RECYCLE_BIN_CYCLE) {
			$newsList = new NewsList();
			$newsList->getConditionBuilder()->add('news.isDeleted = 1');
			$newsList->getConditionBuilder()->add('news.deleteTime < ?', array(TIME_NOW - NEWS_ENABLE_EMPTY_RECYCLE_BIN_CYCLE * 86400));
			$newsList->readObjects();
			
			if (count($newsList->getObjects())) {
				// delete news
				$action = new NewsAction($newsList->getObjects(), 'delete');
				$action->executeAction();
			}
		}
	}
}
