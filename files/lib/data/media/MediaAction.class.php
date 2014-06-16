<?php
namespace news\data\media;
use news\data\media\MediaEditor;
use news\data\media\MediaList;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\upload\DefaultUploadFileValidationStrategy;
use wcf\system\WCF;
use wcf\util\FileUtil;

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
	public function validateGetMediaManagementBrowser() { /* nothing */ }

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

	/**
	 * Validates the upload action.
	 */
	public function validateUpload() {
		WCF::getSession()->checkPermissions(array('user.news.picture.canUpload'));

		if (count($this->parameters['__files']->getFiles()) != 1) {
			throw new UserInputException('files');
		}

		// check max filesize, allowed file extensions etc.
		$this->parameters['__files']->validateFiles(new DefaultUploadFileValidationStrategy(WCF::getSession()->getPermission('user.news.picture.maxSize'), explode("\n", WCF::getSession()->getPermission('user.news.picture.allowedExtensions'))));
	}

	/**
	 * Handles uploaded files.
	 */
	public function upload() {
		// save files
		$files = $this->parameters['__files']->getFiles();
		$file = $files[0];

		try {
			if (!$file->getValidationErrorType()) {
				$data = array(
					'title' => $file->getFilename(),
					'fileHash' => sha1_file($file->getLocation()),
					'fileExtension' => $file->getFileExtension(),
					'filesize' => $file->getFilesize()
				);

				// save file
				$picture = MediaEditor::create($data);

				// move uploaded file
				if (@copy($file->getLocation(), $picture->getLocation())) {
					@unlink($file->getLocation());

					// return result
					return array(
						'pictureID' => $picture->pictureID,
						'title' => $picture->title,
						'filesize' => $picture->filesize,
						'formattedFilesize' => FileUtil::formatFilesize($picture->filesize),
						'url' => $picture->getURL()
					);
				} else {
					// moving failed; delete file
					$editor = new MediaEditor($picture);
					$editor->delete();
					throw new UserInputException('picture', 'uploadFailed');
				}
			}
		}
		catch (UserInputException $e) {
			$file->setValidationErrorType($e->getType());
		}

		return array('errorType' => $file->getValidationErrorType());
	}
}
