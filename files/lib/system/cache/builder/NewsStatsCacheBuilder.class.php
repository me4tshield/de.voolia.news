<?php
namespace news\system\cache\builder;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the number of all news, news per day and of all news comments.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsStatsCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::$maxLifetime
	 */
	protected $maxLifetime = 300;

	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array();

		// amount of news
		$sql = "SELECT	COUNT(*) AS amount
			FROM	news".WCF_N."_news
			WHERE	isActive = 1
			AND	isDeleted = 0
			AND	isPublished = 1";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$data['news'] = $statement->fetchColumn();

		// news per day
		$days = ceil((TIME_NOW - NEWS_INSTALL_DATE) / 86400);
		if ($days <= 0) $days = 1;
		$data['newsPerDay'] = $data['news'] / $days;
		// workaround for lazy people ;)
		$data['newsPerDay'] = round($data['newsPerDay'], 2);

		// number of news comments
		$sql = "SELECT 	SUM(comments) AS count
			FROM 	news".WCF_N."_news
			WHERE	comments > 0";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$data['comments'] = $statement->fetchColumn();

		return $data;
	}
}
