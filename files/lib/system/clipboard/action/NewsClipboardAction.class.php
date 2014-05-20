<?php
namespace news\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\WCF;

/**
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 * @subpackage	system.clipboard.action
 * @category	Community Framework
 */
class NewsClipboardAction extends AbstractClipboardAction {
	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$actionClassActions
	 */
	protected $actionClassActions = array('enable', 'disable', 'trash', 'restore', 'delete');

	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$supportedActions
	 */
	protected $supportedActions = array('enable', 'disable', 'trash', 'restore', 'delete');

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::execute()
	 */
	public function execute(array $objects, ClipboardAction $action) {
		$item = parent::execute($objects, $action);

		if ($item === null) {
			return null;
		}

		// handle actions
		switch ($action->actionName) {
			case 'trash':
				// show confirm dialog
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.voolia.news.entry.trash.confirmMessage', array(
					'count' => $item->getCount()
				)));
				// show template for delete reason
				$item->addInternalData('template', WCF::getTPL()->fetch('newsEntryDeleteReason', 'news'));
			break;

			case 'delete':
				// show confirm dialog
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.voolia.news.entry.delete.confirmMessage', array(
					'count' => $item->getCount()
				)));
				break;
		}

		return $item;
	}

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getClassName()
	 */
	public function getClassName() {
		return 'news\data\news\NewsAction';
	}

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getTypeName()
	 */
	public function getTypeName() {
		return 'de.voolia.news.entry';
	}

	/**
	 * Returns the ids of the entries which can be trashed.
	 * 
	 * @return	array<integer>
	 */
	protected function validateTrash() {
		// check permissions
		if (!WCF::getSession()->getPermission('mod.news.canDeleteNews')) {
			return array();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			if (!$news->isDeleted) $newsIDs[] = $news->newsID;
		}

		return $newsIDs;
	}

	/**
	 * Returns the ids of the entries which can be restored.
	 * 
	 * @return	array<integer>
	 */
	protected function validateRestore() {
		// check permissions
		if (!WCF::getSession()->getPermission('mod.news.canRestoreNews')) {
			return array();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			if ($news->isDeleted) $newsIDs[] = $news->newsID;
		}
		
		return $newsIDs;
	}

	/**
	 * Returns the ids of the entries which can be deleted.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDelete() {
		if (WCF::getSession()->getPermission('mod.news.canDeleteNews')) {
			return array_keys($this->objects);
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			if ($news->isDeleted) $newsIDs[] = $news->newsID;
		}

		return $newsIDs;
	}

	/**
	 * Returns the ids of the entries which can be disabled.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDisable() {
		// check permissions
		if (!WCF::getSession()->getPermission('mod.news.canDeactivateNews')) {
			return array();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			if ($news->isActive) $newsIDs[] = $news->newsID;
		}

		return $newsIDs;
	}

	/**
	 * Returns the ids of the entries which can be enabled.
	 * 
	 * @return	array<integer>
	 */
	protected function validateEnable() {
		// check permissions
		if (!WCF::getSession()->getPermission('mod.news.canActivateNews')) {
			return array();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			if (!$news->isActive) $newsIDs[] = $news->newsID;
		}

		return $newsIDs;
	}
}
