<?php
namespace news\system\moderation\queue\report;
use news\data\news\ViewableNews;
use news\system\moderation\queue\AbstractNewsModerationQueueHandler;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\report\IModerationQueueReportHandler;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;

/**
 * Implementation for news entries of IModerationQueueReportHandler
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsModerationQueueReportHandler extends AbstractNewsModerationQueueHandler implements IModerationQueueReportHandler {
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$definitionName
	 */
	protected $definitionName = 'com.woltlab.wcf.moderation.report';

	/**
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::getReportedObject()
	 */
	public function getReportedObject($objectID) {
		if ($this->isValid($objectID)) {
			return $this->getViewableNews($objectID);
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
		if (!$this->getViewableNews($objectID)->canRead()) {
			return false;
		}

		return true;
	}

	/**
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::getReportedContent()
	 */
	public function getReportedContent(ViewableModerationQueue $queue) {
		WCF::getTPL()->assign(array(
			'news' => new ViewableNews($queue->getAffectedObject())
		));

		// init template
		return WCF::getTPL()->fetch('newsModerationEntry', 'news');
	}
}
