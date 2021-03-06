<?php
namespace news\data;
use wcf\data\DatabaseObject;

/**
 * @author	Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
abstract class NewsDatabaseObject extends DatabaseObject {
	/**
	 * @see	\wcf\data\IStorableObject::getDatabaseTableName()
	 */
	public static function getDatabaseTableName() {
		return 'news'.WCF_N.'_'.static::$databaseTableName;
	}
}
