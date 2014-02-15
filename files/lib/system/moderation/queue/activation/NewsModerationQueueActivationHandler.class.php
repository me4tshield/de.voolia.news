<?php
namespace news\system\moderation\queue\activation;
use news\data\news\NewsAction;
use news\data\news\ViewableNews;
use news\system\moderation\queue\AbstractNewsModerationQueueHandler;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\activation\IModerationQueueActivationHandler;
use wcf\system\WCF;

/**
 * Implementation for news of IModerationQueueActivationHandler.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsModerationQueueActivationHandler extends AbstractNewsModerationQueueHandler implements IModerationQueueActivationHandler {
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$definitionName
	 */
	protected $definitionName = 'com.woltlab.wcf.moderation.activation';

	/**
	 * @see	\wcf\system\moderation\queue\activation\IModerationQueueActivationHandler::enableContent()
	 */
	public function enableContent(ModerationQueue $queue) {
		if ($this->isValid($queue->objectID) && !$this->getViewableNews($queue->objectID)->isActive) {
			$newsAction = new NewsAction(array($this->getViewableNews($queue->objectID)), 'enable');
			$newsAction->executeAction();
		}
	}

	/**
	 * @see	\wcf\system\moderation\queue\activation\IModerationQueueActivationHandler::getDisabledContent()
	 */
	public function getDisabledContent(ViewableModerationQueue $queue) {
		WCF::getTPL()->assign(array(
			'news' => new ViewableNews($queue->getAffectedObject())
		));

		// init template
		return WCF::getTPL()->fetch('newsModerationEntry', 'news');
	}
}
