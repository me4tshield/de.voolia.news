<?php
namespace news\system\importer;
use news\data\news\News;
use news\data\news\NewsEditor;
use wcf\data\user\User;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * News importer.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsEntryImporter extends AbstractImporter {
	/**
	 * @see	\wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'news\data\news\News';

	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		// get user id
		$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);

		// get news id
		if (is_numeric($oldID)) {
			$news = new News($oldID);
			if (!$news->newsID) $data['newsID'] = $oldID;
		}

		// get news categories
		$categoryIDs = array();
		if (!empty($additionalData['categories'])) {
			foreach ($additionalData['categories'] as $oldCategoryID) {
				$newCategoryID = ImportHandler::getInstance()->getNewID('de.voolia.news.category', $oldCategoryID);
				if ($newCategoryID) $categoryIDs[] = $newCategoryID;
			}
		}

		// work-around for unknown username
		if (empty($data['username'])) {
			$user = new User($data['userID']);
			$data['username'] = $user->username;
		}

		// get language by languageCode
		if (!empty($additionalData['languageCode'])) {
			if (($language = LanguageFactory::getInstance()->getLanguageByCode($additionalData['languageCode'])) !== null) {
				$data['languageID'] = $language->languageID;
			}
		}

		// create news
		$news = NewsEditor::create($data);
		$newsEditor = new NewsEditor($news);

		// save the tags from news entry
		if (!empty($additionalData['tags'])) {
			TagEngine::getInstance()->addObjectTags('de.voolia.news.entry', $news->newsID, $additionalData['tags'], ($news->languageID ?: LanguageFactory::getInstance()->getDefaultLanguageID()));
		}

		// update news categories
		$newsEditor->updateCategoryIDs($categoryIDs);

		ImportHandler::getInstance()->saveNewID('de.voolia.news.entry', $oldID, $news->newsID);

		return $news->newsID;
	}
}
