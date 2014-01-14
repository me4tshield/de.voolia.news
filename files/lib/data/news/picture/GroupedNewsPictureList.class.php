<?php
namespace news\data\news\picture;

/**
 * Provides a grouped list of news pictures.
 * 
 * @author	Florian Frantzen <ray176@me.com>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class GroupedNewsPictureList {
	/**
	 * picture list object
	 * @var	\news\data\news\picture\NewsPictureList
	 */
	protected $pictureList = null;

	/**
	 * Creates a new grouped list of news pictures.
	 */
	public function __construct() {
		$this->pictureList = new NewsPictureList();
	}

	/**
	 * @see	\wcf\data\DatabaseObjectList::getObjects()
	 */
	public function getObjects() {
		$groupedList = array();

		foreach ($this->pictureList as $picture) {
			if (!isset($groupedList[$picture->categoryID])) {
				$groupedList[$picture->categoryID] = array();
			}

			$groupedList[$picture->categoryID][] = $picture;
		}

		return $groupedList;
	}

	public function __call($name, array $arguments) {
		if (!method_exists($this, $name)) {
			return call_user_func_array(array($this->pictureList, $name), $arguments);
		}
		return call_user_func_array(array($this, $name), $arguments);
        }
}
