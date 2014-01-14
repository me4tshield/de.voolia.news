<?php
namespace news\data\news\update;

/**
 * Represents a list of news updates.
 * 
 * @author	Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class ViewableNewsUpdateList extends NewsUpdateList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$decoratorClassName
	 */
	public $decoratorClassName = 'news\data\news\update\ViewableNewsUpdate';

	/**
	 * Creates a new ViewableNewsList object.
	 */
	public function __construct() {
		parent::__construct();

		// get user
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ', ';
		$this->sqlSelects .= "user_table.*";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = news_update.userID)";
	}
}
