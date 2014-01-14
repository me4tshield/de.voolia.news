<?php
namespace news\data\news\picture;
use news\data\news\picture\category\NewsPictureCategoryNodeTree;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\upload\DefaultUploadFileValidationStrategy;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * Executes news picture-related actions.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'news\data\news\picture\NewsPictureEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.news.canManageNewsPicture');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.news.canManageNewsPicture');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.news.canManageNewsPicture');

	/**
	 * Validates the 'getPictureList' action.
	 */
	public function validateGetPictureList() {
		if (isset($this->parameters['categoryID'])) {
			$category = CategoryHandler::getInstance()->getCategory($this->parameters['categoryID']);
			if ($category === null) {
				throw new UserInputException('categoryID');
			}
		}
	}

	/**
	 * Returns picture list.
	 * 
	 * @return	array
	 */
	public function getPictureList() {
		if (isset($this->parameters['categoryID'])) {
			$category = CategoryHandler::getInstance()->getCategory($this->parameters['categoryID']);

			$categoryIDs = array($category->categoryID);
			foreach ($category->getChildCategories() as $childCategory) {
				$categoryIDs[] = $childCategory->categoryID;
			}

			// fetch news pictures
			$pictureList = new GroupedNewsPictureList();
			$pictureList->getConditionBuilder()->add('news_picture.categoryID IN (?)', array($categoryIDs));
			$pictureList->readObjects();

			// assign variables
			WCF::getTPL()->assign(array(
				'categoryID' => $this->parameters['categoryID'],
				'category' => $category,
				'childCategories' => $category->getChildCategories(),
				'pictures' => $pictureList->getObjects()
			));

			return array(
				'categoryID' => $this->parameters['categoryID'],
				'template' => WCF::getTPL()->fetch('groupedPictureList', 'news')
			);
		} else {
			$categoryID = 0;

			// get first category
			$categories = CategoryHandler::getInstance()->getCategories('de.voolia.news.picture.category');
			while (!empty($categories)) {
				$tCategory = array_shift($categories);
				if ($tCategory->parentCategoryID == 0) {
					$categoryID = $tCategory->categoryID;
					$category = $tCategory;
					break;
				}
			}

			if ($categoryID) {
				// fetch category node tree
				$categoryTree = new NewsPictureCategoryNodeTree('de.voolia.news.picture.category');
				$categoryList = $categoryTree->getIterator();
				$categoryList->setMaxDepth(0);

				$categoryIDs = array($categoryID);
				foreach ($category->getChildCategories() as $childCategory) {
					$categoryIDs[] = $childCategory->categoryID;
				}

				$pictureList = new GroupedNewsPictureList();
				$pictureList->getConditionBuilder()->add('news_picture.categoryID IN (?)', array($categoryIDs));
				$pictureList->readObjects();

				// assign variables
				WCF::getTPL()->assign(array(
					'categoryID' => $categoryID,
					'categoryList' => $categoryList,
					'pictures' => $pictureList->getObjects()
				));
			}

			// fetch aleady selected/uploaded picture
			if ($this->parameters['pictureID']) {
				$picture = new NewsPicture($this->parameters['pictureID']);
				if ($picture->pictureID) {
					WCF::getTPL()->assign('picture', $picture);
				}
			}

			return array(
				'categoryID' => $categoryID,
				'template' => WCF::getTPL()->fetch('pictureListInspector', 'news')
			);
		}
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
					'fileExtension' => $file->getFileExtension(),
					'fileType' => $file->getMimeType(),
					'fileHash' => sha1_file($file->getLocation()),
					'filesize' => $file->getFilesize(),
					'uploadTime' => TIME_NOW
				);

				// save file
				$picture = NewsPictureEditor::create($data);

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
					$editor = new NewsPictureEditor($picture);
					$editor->delete();
					throw new UserInputException('picture', 'uploadFailed');
				}
			}
		} catch (UserInputException $e) {
			$file->setValidationErrorType($e->getType());
		}

		return array('errorType' => $file->getValidationErrorType());
	}
}
