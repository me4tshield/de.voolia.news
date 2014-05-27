<?php
namespace news\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Dashboard sidebar box for sorting with letters.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class LetterSortDashboardSidebarBox extends AbstractSidebarDashboardBox {
	/**
	 * letter
	 * @var	string
	 */
	public $letter = '';

	/**
	 * available letters
	 * @var	string
	 */
	public static $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		// letters
		if (isset($_REQUEST['letter']) && mb_strlen($_REQUEST['letter']) == 1 && mb_strpos(self::$availableLetters, $_REQUEST['letter']) !== false) {
			$this->letter = $_REQUEST['letter'];
		}

		if (!empty($this->letter)) {
			if ($this->letter == '#') {
				$this->objectList->getConditionBuilder()->add("SUBSTRING(news.subject,1,1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
			} else {
				$this->objectList->getConditionBuilder()->add("news.subject LIKE ?", array($this->letter.'%'));
			}
		}

		$this->fetched();
	}

	/**
	 * @see	\wcf\system\dashboard\box\AbstractSidebarDashboardBox::render()
	 */
	protected function render() {
		WCF::getTPL()->assign(array(
			'letters' => str_split(self::$availableLetters),
			'letter' => $this->letter,
			'categoryID' => (!empty($this->page->categoryID) ? $this->page->categoryID : null)
		));

		return WCF::getTPL()->fetch('dashboardSidebarBoxLetterSort', 'news');
	}
}
