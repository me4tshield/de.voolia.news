<?php
namespace news\form;
use news\data\news\update\NewsUpdateAction;
use news\data\news\ViewableNews;
use news\system\NEWSCore;
use wcf\form\MessageForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * @author	Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdateAddForm extends MessageForm {
	/**
	 * @see	\wcf\page\IPage::$action
	 */
	public $action = 'add';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('user.news.canAddNews');

	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_CONTENT_NEWS');

	/**
	 * news id
	 * @var	integer
	 */
	public $newsID = 0;

	/**
	 * news object
	 * @var	\news\data\news\ViewableNews
	 */
	public $news = null;

	/**
	 * set news as new by update
	 * @var  integer
	 */
	public $setNewsAsNew = 0;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->newsID = intval($_REQUEST['id']);
		$this->news = ViewableNews::getViewableNews($this->newsID);
		if ($this->news === null) {
			throw new IllegalLinkException();
		}

		// check news permissions
		if (!$this->news->isEditable()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['setNewsAsNew'])) $this->setNewsAsNew = intval($_POST['setNewsAsNew']);
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		$this->objectAction = new NewsUpdateAction(array(), 'create', array(
			'data' => array(
				'newsID' => $this->newsID,
				'time' => TIME_NOW,
				'userID' => (WCF::getUser()->userID ?: null),
				'username' => (WCF::getUser()->userID ? WCF::getUser()->username : $this->username),
				'subject' => $this->subject,
				'text' => $this->text,
				'enableSmilies' => $this->enableSmilies,
				'enableHtml' => $this->enableHtml,
				'enableBBCodes' => $this->enableBBCodes
			)
		));
		$this->objectAction->executeAction();

		// update news entry
        	$sql = "UPDATE	news".WCF_N."_news
             	   	SET	newsUpdates = newsUpdates + 1
             	   	WHERE	newsID = ?";
             	$statement = WCF::getDB()->prepareStatement($sql);
             	$statement->execute(array($this->newsID));

	     	// set news as new
	     	if ($this->setNewsAsNew) {
        		$sql = "UPDATE	news".WCF_N."_news
             	   		SET	time = ?
	     	   		WHERE	newsID = ?";
	     	   	$statement = WCF::getDB()->prepareStatement($sql);
	     	   	$statement->execute(array(TIME_NOW, $this->newsID));

		     	$sql = "DELETE FROM	wcf".WCF_N."_tracked_visit
		     		WHERE		objectTypeID = ?
		     		AND		objectID = ?";
		     	$statement = WCF::getDB()->prepareStatement($sql);
		     	$statement->execute(array(VisitTracker::getInstance()->getObjectTypeID('de.voolia.news.entry'), $this->newsID));
		}

		// redirect to news entry
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'object' => $this->news
		)));
		exit;
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// add breadcrumbs
		NEWSCore::getInstance()->setBreadcrumbs(array(), null, $this->news->getDecoratedObject());
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'newsID' => $this->newsID,
			'news' => $this->news
		));
	}
}
