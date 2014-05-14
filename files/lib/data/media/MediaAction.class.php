<?php
namespace news\data\media;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Functions to edit a media object.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 * @subpackage	data.media
 * @category	voolia News
 */
class MediaAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'news\data\media\MediaEditor';
}
