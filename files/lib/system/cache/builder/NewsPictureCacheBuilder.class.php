<?php
namespace news\system\cache\builder;
use news\data\news\picture\NewsPictureList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$pictureList = new NewsPictureList();
		$pictureList->readObjects();

		return $pictureList->getObjects();
	}
}
