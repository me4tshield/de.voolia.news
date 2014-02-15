<?php
namespace news\system\importer;
use news\data\news\update\NewsUpdate;
use news\data\news\update\NewsUpdateEditor;
use wcf\data\user\User;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;

/**
 * Importer for news updates.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsEntryUpdateImporter extends AbstractImporter {
	/**
	 * @see	\wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'news\data\news\update\NewsUpdate';

	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		// get user id
		$data['newsID'] = ImportHandler::getInstance()->getNewID('de.voolia.news.entry', $data['newsID']);
		$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);

		// create news update
		$newsUpdate = NewsUpdateEditor::create($data);
		$newsUpdateEditor = new NewsUpdateEditor($newsUpdate);

		ImportHandler::getInstance()->saveNewID('de.voolia.news.entry.update', $oldID, $newsUpdate->updateID);

		return $newsUpdate->updateID;
	}
}
