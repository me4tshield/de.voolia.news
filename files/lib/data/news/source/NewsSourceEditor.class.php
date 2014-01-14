<?php
namespace news\data\news\source;
use wcf\data\DatabaseObjectEditor;

/**
 * Functions to edit a news source.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsSourceEditor extends DatabaseObjectEditor {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = 'news\data\news\source\NewsSource';
}
