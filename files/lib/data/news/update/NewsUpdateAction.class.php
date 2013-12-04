<?php
namespace news\data\news\update;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes news update-related actions.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdateAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'news\data\news\update\NewsUpdateEditor';
}
