<?php
namespace news\form;
use news\data\news\update\NewsUpdate;
use news\data\news\update\NewsUpdateAction;
use news\data\news\ViewableNews;
use wcf\form\MessageForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * @author	Florian Frantzen <ray176@voolia.de>, Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdateEditForm extends NewsUpdateAddForm {
	/**
	 * @see	\wcf\page\IPage::$action
	 */
	public $action = 'edit';

	/**
	 * update id
	 * @var	integer
	 */
	public $updateID = 0;

	/**
	 * news update object
	 * @var	\news\data\news\update\NewsUpdate
	 */
	public $update = null;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();

		if (isset($_REQUEST['id'])) $this->updateID = intval($_REQUEST['id']);
		$this->update = new NewsUpdate($this->updateID);
		if (!$this->update->updateID) {
			throw new IllegalLinkException();
		}

		$this->newsID = $this->update->newsID;
		$this->news = ViewableNews::getViewableNews($this->newsID);

		// check news permissions
		if (!$this->news->isEditable()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		MessageForm::save();

		// save the news update
		$this->objectAction = new NewsUpdateAction(array($this->update), 'update', array(
			'data' => array(
				'subject' => $this->subject,
				'text' => $this->text
			)
		));
		$this->objectAction->executeAction();

		$this->saved();

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

		if (!count($_POST)) {
			$this->subject = $this->update->subject;
			$this->text = $this->update->text;
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'updateID' => $this->updateID
		));
	}
}
