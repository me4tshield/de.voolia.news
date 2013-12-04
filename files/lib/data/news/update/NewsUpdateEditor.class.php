<?php
namespace news\data\news\update;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit news updates.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUpdateEditor extends DatabaseObjectEditor {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = 'news\data\news\update\NewsUpdate';
}
