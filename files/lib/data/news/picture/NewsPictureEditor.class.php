<?php
namespace news\data\news\picture;
use news\system\cache\builder\NewsPictureCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit news pictures.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = 'news\data\news\picture\NewsPicture';

	/**
	 * @see	\wcf\data\IEditableObject::deleteAll()
	 */
	public static function deleteAll(array $objectIDs = array()) {
		$list = new NewsPictureList();
		$list->setObjectIDs($objectIDs);
		$list->readObjects();
		foreach ($list as $object) {
			$editor = new NewsPictureEditor($object);
			$editor->deletePicture();
		}

		return parent::deleteAll($objectIDs);
	}

	/**
	 * Deletes the image file for this news picture
	 */
	public function deletePicture() {
		if (file_exists($this->getLocation())) {
			@unlink($this->getLocation());
		}
	}

	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		NewsPictureCacheBuilder::getInstance()->reset();
	}
}
