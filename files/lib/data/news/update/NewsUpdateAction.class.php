<?php
namespace news\data\news\update;
use news\data\news\News;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes news update-related actions.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdateAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'news\data\news\update\NewsUpdateEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('user.news.canAddNewsUpdate');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('user.news.canAddNewsUpdate');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('user.news.canAddNewsUpdate');

	/**
	 * @see	\wcf\data\IDeleteAction::validateDelete()
	 */
	public function validateDelete() {
		// read objects
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
	}

	/**
	 * @see	\wcf\data\IDeleteAction::delete()
	 */
	public function delete() {
		parent::delete();

		foreach ($this->objects as $newsUpdateEditor) {
			$news = new News($newsUpdateEditor->newsID);
			$news->updateNewsUpdates();
		}
	}
}
