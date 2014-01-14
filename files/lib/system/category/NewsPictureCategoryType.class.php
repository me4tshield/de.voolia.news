<?php
namespace news\system\category;
use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

/**
 * @author	Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCategoryType extends AbstractCategoryType {
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$forceDescription
	 */
	protected $hasDescription = false;

	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$langVarPrefix
	 */
	protected $langVarPrefix = 'news.acp.menu.link.news.picture.category.list';

	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$maximumNestingLevel
	 */
	protected $maximumNestingLevel = 1;

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
		return WCF::getSession()->getPermission('admin.news.canManageNewsPictureCategory');
	}
}
