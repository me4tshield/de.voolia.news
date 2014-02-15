<?php
namespace news\data\category;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\category\CategoryHandler;
use wcf\system\category\CategoryPermissionHandler;
use wcf\system\request\LinkHandler;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Represents a news category.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCategory extends AbstractDecoratedCategory implements IBreadcrumbProvider {
	const OBJECT_TYPE = 'de.voolia.news.category';

	/**
	 * acl permissions for this news category
	 * @var	array<boolean>
	 */
	protected $permissions = null;

	/**
	 * subscribed news categories
	 * @var	array<integer>
	 */
	protected static $subscribedCategories = null;

	/**
	 * Returns true if the news category is accessible.
	 * 
	 * @return	boolean
	 */
	public function isAccessible() {
		if ($this->getObjectType()->objectType != self::OBJECT_TYPE) return false;

		// check news permissions
		return $this->getPermission('canViewCategory');
	}

	/**
	 * Returns true, if the current user can use this news category.
	 * 
	 * @return	boolean
	 */
	public function canUseCategory() {
		// check news permissions
		if ($this->getPermission('canUseCategory') && WCF::getSession()->getPermission('user.news.canAddNews')) {
			return true;
		}
		
		return false;
	}

	/**
	 * Returns a list with category ids of accessible news categories.
	 * 
	 * @param	array		$permissions
	 * @return	array<integer>
	 */
	public static function getAccessibleCategoryIDs(array $permissions = array('canViewCategory')) {
		$categoryIDs = array();
		foreach (CategoryHandler::getInstance()->getCategories(self::OBJECT_TYPE) as $category) {
			$result = true;
			$category = new NewsCategory($category);
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}

			if ($result) {
				$categoryIDs[] = $category->categoryID;
			}
		}

		return $categoryIDs;
	}

	/**
	 * @see	\wcf\system\breadcrumb\IBreadcrumbProvider::getBreadcrumb()
	 */
	public function getBreadcrumb() {
		return new Breadcrumb(WCF::getLanguage()->get($this->title), LinkHandler::getInstance()->getLink('NewsOverview', array(
			'application' => 'news',
			'object' => $this->getDecoratedObject()
		)));
	}

	/**
	 * @see	\wcf\data\IPermissionObject::getPermission()
	 */
	public function getPermission($permission) {
		if ($this->permissions === null) {
			$this->permissions = CategoryPermissionHandler::getInstance()->getPermissions($this->getDecoratedObject());
		}

		if (isset($this->permissions[$permission])) {
			return $this->permissions[$permission];
		}

		return true;
	}

	/**
	 * Returns the list of the subscribed news categories.
	 * 
	 * @return	array<integer>
	 */
	public static function getSubscribedCategoryIDs() {
		if (self::$subscribedCategories === null) {
			self::$subscribedCategories = array();

			if (WCF::getUser()->userID) {
				// load the user storage data
				UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));

				// get the ids
				$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), 'newsSubscribedCategories');

				// if the cache does not exist, or is outdated
				if ($data[WCF::getUser()->userID] === null) {
					$objectTypeID = UserObjectWatchHandler::getInstance()->getObjectTypeID(self::OBJECT_TYPE);

					$sql = "SELECT	objectID
						FROM	wcf".WCF_N."_user_object_watch
						WHERE	objectTypeID = ?
						AND	userID = ?";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute(array($objectTypeID, WCF::getUser()->userID));
					while ($row = $statement->fetchArray()) {
						self::$subscribedCategories[] = $row['objectID'];
					}

					// update the user storage data
					UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'newsSubscribedCategories', serialize(self::$subscribedCategories));
				} else {
					self::$subscribedCategories = unserialize($data[WCF::getUser()->userID]);
				}
			}
		}

		return self::$subscribedCategories;
	}

	/**
	 * Returns true, if the user has subscribed to the news category.
	 * 
	 * @return	boolean
	 */
	public function isSubscribed() {
		return (in_array($this->categoryID, self::getSubscribedCategoryIDs()));
	}
}
