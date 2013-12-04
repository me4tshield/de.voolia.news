<?php
namespace news\data\news\source;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Functions to edit a news source.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsSourceAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'news\data\news\source\NewsSourceEditor';
}
