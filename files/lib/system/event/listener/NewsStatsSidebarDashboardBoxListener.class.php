<?php
namespace news\system\event\listener;
use news\system\cache\builder\NewsStatsCacheBuilder;
use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * Provides news statistic in the statistic dashboard box.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsStatsSidebarDashboardBoxListener implements IEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		WCF::getTPL()->assign(array(
			'newsStatsDashboardBox' => NewsStatsCacheBuilder::getInstance()->getData()
		));
	}
}
