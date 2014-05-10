<?php
namespace news\data\news\picture;

/**
 * Any displayable news picture type should implement this interface.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
interface INewsPicture {
	/**
	 * Returns the url to this picture.
	 * 
	 * @return	string
	 */
	public function getURL();
}
