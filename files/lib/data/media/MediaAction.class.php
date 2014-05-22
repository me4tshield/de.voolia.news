<?php
namespace news\data\media;
use news\data\media\MediaList;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Functions to edit a media object.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 * @subpackage	data.media
 * @category	voolia News
 */
class MediaAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'news\data\media\MediaEditor';

	/**
	 * Set the items per page
	 */
	public $itemsPerPage = 10;

	/**
	 * Validates the getMediaManagementBrowser
	 */
	public function validateGetMediaManagementBrowser() {
		/** nothing to do here **/
	}

	/**
	 * Returns a list with media managmenet items
	 */
	public function getMediaManagementBrowser() {
		$mediaData = $this->fetchMediaObjects();

		WCF::getTPL()->assign(array(
			'mediaData' => $mediaData
		));

		return array(
			'template' => WCF::getTPL()->fetch('mediaManagementBrowser', 'news')
		);
	}

	/**
	 * Fetches a list of media objects.
	 */
	protected function fetchMediaObjects($pageNo = 1, $searchString = '') {
		$mediaList = new MediaList();
		if (!empty($searchString)) $mediaList->getConditionBuilder()->add("news_media.title LIKE ?", array($searchString . '%'));
		$mediaList->sqlLimit = $this->itemsPerPage;
		$mediaList->sqlOffset = ($pageNo - 1) * $this->itemsPerPage;
		$mediaList->readObjects();

		return array(
			'mediaList' => $mediaList
		);
	}
}
