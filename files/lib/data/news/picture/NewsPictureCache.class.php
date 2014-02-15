<?php
namespace news\data\news\picture;
use news\system\cache\builder\NewsPictureCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCache extends SingletonFactory {
	/**
	 * news pictures
	 * @var	array<\news\data\news\picture\NewsPicture>
	 */
	public $pictures = array();

	/**
	 * @see	wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->pictures = NewsPictureCacheBuilder::getInstance()->getData();
	}

	/**
	 * Returns the news picture with the given id from cache.
	 * 
	 * @param	integer		$pictureID
	 * @return	\news\data\news\picture\NewsPicture
	 */
	public function getPicture($pictureID) {
		if (isset($this->pictures[$pictureID])) {
			return $this->pictures[$pictureID];
		}

		return null;
	}
}
