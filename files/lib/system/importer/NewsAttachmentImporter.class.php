<?php
namespace news\system\importer;
use news\data\news\News;
use news\data\news\NewsEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractAttachmentImporter;
use wcf\system\importer\ImportHandler;

/**
 * Importer for attachments of news entries.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsAttachmentImporter extends AbstractAttachmentImporter {
	/**
	 * Creates a object for NewsAttachmentImporter.
	 */
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', 'de.voolia.news.entry');
		$this->objectTypeID = $objectType->objectTypeID;
	}
	
	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		// get news id
		$data['objectID'] = ImportHandler::getInstance()->getNewID('de.voolia.news.entry', $data['objectID']);
		if (!$data['objectID']) return 0;
		
		$attachmentID = parent::import($oldID, $data, $additionalData);
		if ($attachmentID && $attachmentID != $oldID) {

			// get the news
			$news = new News($data['objectID']);
			
			// update news text with the new attachment id
			if (($newText = $this->fixEmbeddedAttachments($news->text, $oldID, $attachmentID)) !== false) {
				$newsEditor = new NewsEditor($news);
				$newsEditor->update(array(
					'text' => $newText
				));
			}
		}
		
		return $attachmentID;
	}
}
