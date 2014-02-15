<?php
namespace news\system\importer;
use wcf\system\importer\AbstractCommentResponseImporter;

/**
 * Import the news comment response.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCommentResponseImporter extends AbstractCommentResponseImporter {
	/**
	 * @see	\wcf\system\importer\AbstractCommentResponseImporter::$objectTypeName
	 */
	protected $objectTypeName = 'de.voolia.news.comment';
}
