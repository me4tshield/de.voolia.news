<?php
namespace news\system\importer;
use wcf\data\like\Like;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractLikeImporter;
use wcf\system\importer\ImportHandler;

/**
 * Importer for news likes.
 *
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsLikeImporter extends AbstractLikeImporter {
	/**
	 * Creates a object for NewsCommentImporter.
	 */
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.like.likeableObject', 'de.voolia.news.likeableNews');
		$this->objectTypeID = $objectType->objectTypeID;
	}

	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		$data['objectID'] = ImportHandler::getInstance()->getNewID('de.voolia.news.entry', $data['objectID']);

		return parent::import($oldID, $data);
	}
}
