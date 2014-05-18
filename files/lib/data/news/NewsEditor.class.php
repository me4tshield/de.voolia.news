<?php
namespace news\data\news;
use news\system\cache\builder\NewsStatsCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Functions to edit a news entry.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsEditor extends DatabaseObjectEditor {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = 'news\data\news\News';

	/**
	 * Updates news category ids.
	 * 
	 * @param	array<integer>		$categoryIDs
	 */
	public function updateCategoryIDs(array $categoryIDs = array()) {
		// remove old assigns
		$sql = "DELETE FROM	news".WCF_N."_news_to_category
			WHERE		newsID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->newsID));

		// new categories
		if (!empty($categoryIDs)) {
			WCF::getDB()->beginTransaction();

			$sql = "INSERT INTO	news".WCF_N."_news_to_category
						(categoryID, newsID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($categoryIDs as $categoryID) {
				$statement->execute(array(
					$categoryID,
					$this->newsID
				));
			}

			WCF::getDB()->commitTransaction();
		}
	}

	/**
	 * Update the news counter for the author.
	 */
	public static function updateNewsCounter(array $users) {
		$sql = "UPDATE	wcf".WCF_N."_user
			SET	newsEntries = newsEntries + ?
			WHERE	userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($users as $userID => $news) {
			$statement->execute(array($news, $userID));
		}
	}

	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetNewsStatsCache() {
		NewsStatsCacheBuilder::getInstance()->reset();
	}
}
