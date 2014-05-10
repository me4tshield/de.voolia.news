<?php
namespace news\data\news\picture;
use wcf\system\WCF;

/**
 * Default news picture.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class DefaultNewsPicture implements INewsPicture {
	/**
	 * @see	\news\data\news\picture\INewsPicture::getURL()
	 */
	public function getURL() {
		return WCF::getPath('news') .'images/news/dummyPicture.png';
	}
}
