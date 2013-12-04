<?php
namespace news\acp\form;
use wcf\form\AbstractForm;
use wcf\system\WCF;

/**
 * Shows the news picture edit form.
 * 
 * @author	Pascal Bade, Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPictureEditForm extends NewsPictureAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.acp.menu.link.news.picture.list';

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		if (empty($_POST)) {
			$this->categoryID = $this->picture->categoryID;
			$this->title = $this->picture->title;
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit'
		));
	}
}
