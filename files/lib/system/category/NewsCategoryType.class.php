<?php
namespace news\system\category;
use wcf\data\category\CategoryEditor;
use wcf\system\category\AbstractCategoryType;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\WCF;

/**
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategoryType extends AbstractCategoryType {
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$forceDescription
	 */
	protected $hasDescription = false;

	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$langVarPrefix
	 */
	protected $langVarPrefix = 'wcf.news.category';

	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$maximumNestingLevel
	 */
	protected $maximumNestingLevel = 1;

	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$objectTypes
	 */
	protected $objectTypes = array('com.woltlab.wcf.acl' => 'de.voolia.news.category');

	/**
	 * @see	\wcf\system\category\ICategoryType::getApplication()
	 */
	public function getApplication() {
		return 'news';
	}

	/**
	 * @see	\wcf\system\category\ICategoryType::canAddCategory()
	 */
	public function canAddCategory() {
		return $this->canEditCategory();
	}

	/**
	 * @see	\wcf\system\category\ICategoryType::canDeleteCategory()
	 */
	public function canDeleteCategory() {
		return $this->canEditCategory();
	}

	/**
	 * @see	\wcf\system\category\ICategoryType::canEditCategory()
	 */
	public function canEditCategory() {
		return WCF::getSession()->getPermission('admin.news.canManageCategory');
	}

	/**
	 * @see	\wcf\system\category\ICategoryType::afterDeletion()
	 */
	public function afterDeletion(CategoryEditor $categoryEditor) {
		// delete the news category subscriptions
		UserObjectWatchHandler::getInstance()->deleteObjects('de.voolia.news.category', array($categoryEditor->categoryID));
	}
}
