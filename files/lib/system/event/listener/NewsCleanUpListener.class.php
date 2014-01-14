<?php
namespace news\system\event\listener;
use news\data\news\picture\NewsPictureAction;
use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * @author	Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCleanUpListener implements IEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$pictureIDs = array();

		// delete obsolete picture uploads
		$sql = "SELECT		news_picture.pictureID
			FROM		news".WCF_N."_news_picture news_picture
			LEFT JOIN	news".WCF_N."_news news ON (news.pictureID = news_picture.pictureID)
			WHERE		news_picture.categoryID IS NULL
			AND		news.newsID IS NULL
			AND		news_picture.uploadTime < ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(TIME_NOW - 86400));
		while ($row = $statement->fetchArray()) {
			$pictureIDs[] = $row['pictureID'];
		}

		$action = new NewsPictureAction($pictureIDs, 'delete');
		$action->executeAction();
	}
}
