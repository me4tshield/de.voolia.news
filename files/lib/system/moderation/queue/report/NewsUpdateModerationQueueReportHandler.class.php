<?php
namespace news\system\moderation\queue\report;
use news\data\news\update\ViewableNewsUpdate;
use news\system\moderation\queue\AbstractNewsUpdateModerationQueueHandler;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\report\IModerationQueueReportHandler;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;

/**
 * Implementation for news updates of IModerationQueueReportHandler
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdateModerationQueueReportHandler extends AbstractNewsUpdateModerationQueueHandler implements IModerationQueueReportHandler {
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$definitionName
	 */
	protected $definitionName = 'com.woltlab.wcf.moderation.report';

	/**
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::getReportedObject()
	 */
	public function getReportedObject($objectID) {
		if ($this->isValid($objectID)) {
			return $this->getViewableNewsUpdate($objectID);
		}

		return null;
	}

	/**
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::canReport()
	 */
	public function canReport($objectID) {
		if (!$this->isValid($objectID)) {
			return false;
		}

		// Make sure, that the news is accessible
		if (!$this->getViewableNewsUpdate($objectID)->isVisible()) {
			return false;
		}

		return true;
	}

	/**
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::getReportedContent()
	 */
	public function getReportedContent(ViewableModerationQueue $queue) {
		WCF::getTPL()->assign(array(
			'news' => new ViewableNewsUpdate($queue->getAffectedObject())
		));

		// init template
		return WCF::getTPL()->fetch('newsUpdateModerationEntry', 'news');
	}
}
