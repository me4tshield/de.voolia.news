<?php
namespace news\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\language\LanguageFactory;
use wcf\system\tagging\TypedTagCloud;
use wcf\system\WCF;

/**
 * Dashboardbox with news tags.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsTagCloudDashboardSidebarBox extends AbstractSidebarDashboardBox {
	/**
	 * tag cloud
	 * @var	\wcf\system\tagging\TypedTagCloud
	 */
	public $tagCloud = null;

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		if (MODULE_TAGGING) {
			$languageIDs = array();
			if (LanguageFactory::getInstance()->multilingualismEnabled()) {
				$languageIDs = WCF::getUser()->getLanguageIDs();
			}

			// get tag cloud for news entries
			$this->tagCloud = new TypedTagCloud('de.voolia.news.entry', $languageIDs);
		}

		$this->fetched();
	}

	/**
	 * @see	\wcf\system\dashboard\box\AbstractContentDashboardBox::render()
	 */
	protected function render() {
		if ($this->tagCloud === null) {
			return '';
		}

		WCF::getTPL()->assign(array(
			'tags' => $this->tagCloud->getTags()
		));

		return WCF::getTPL()->fetch('dashboardSidebarBoxTagCloud', 'news');
	}
}
