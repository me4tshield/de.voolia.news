<?php
/**
 * @author	Pascal Bade
 * @copyright	2012-2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
require_once('./global.php');
wcf\system\request\RequestHandler::getInstance()->handle('news', true);
