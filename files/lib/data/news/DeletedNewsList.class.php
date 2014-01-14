<?php
namespace news\data\news;
use news\data\category\NewsCategory;

/**
 * Represents a list of deleted news.
 *
 * @author	Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class DeletedNewsList extends ViewableNewsList {
	/**
	 * Creates a new DeletedEntryList object.
	 */
	public function __construct() {
		parent::__construct();

		// accessible news categories
		$accessibleCategoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if (!empty($accessibleCategoryIDs)) $this->getConditionBuilder()->add('news.newsID IN (SELECT newsID FROM news'.WCF_N.'_news_to_category WHERE categoryID IN (?))', array($accessibleCategoryIDs));
		else $this->getConditionBuilder()->add('1=0');

		// load only deleted news
		$this->getConditionBuilder()->add('news.isDeleted = ?', array(1));
	}
}
