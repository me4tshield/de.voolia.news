<?php
namespace news\system\importer;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCategoryImporter;

/**
 * News category importer.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryImporter extends AbstractCategoryImporter {
	/**
	 * @see	\wcf\system\importer\AbstractCommentImporter::$objectTypeName
	 */
	protected $objectTypeName = 'de.voolia.news.category';
	
	/**
	 * Create a object for NewsCategoryImporter
	 */
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.category', $this->objectTypeName);
		$this->objectTypeID = $objectType->objectTypeID;
	}
}
