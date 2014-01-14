<?php 
namespace news\data\news;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents a list with unread news.
 *
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class UnreadNewsList extends AccessibleNewsList {
	/**
	 * Creates a new UnreadNewsList object.
	 */
	public function __construct() {
		parent::__construct();

		$this->getConditionBuilder()->add("news.time > ?", array(VisitTracker::getInstance()->getVisitTime('de.voolia.news.entry')));
		$this->getConditionBuilder()->add("tracked_visit.visitTime IS NULL");

		$this->sqlConditionJoins = "LEFT JOIN wcf".WCF_N."_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.voolia.news.entry')." AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = ".WCF::getUser()->userID.")";
	}
}
