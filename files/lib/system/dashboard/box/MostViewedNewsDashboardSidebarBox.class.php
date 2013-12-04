<?php
namespace news\system\dashboard\box;
use news\data\news\AccessibleNewsList;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Dashboard sidebar box for most viewed news entries.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MostViewedNewsDashboardSidebarBox extends AbstractSidebarDashboardBox {
	/**
	 * most viewed news entries list
	 * @var	\news\data\news\AccessibleNewsList
	 */
	public $vooliaNewsList = null;

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		$this->vooliaNewsList = new AccessibleNewsList();
		$this->vooliaNewsList->sqlLimit = NEWS_DASHBOARD_SIDEBAR_ENTRIES;
		$this->vooliaNewsList->getConditionBuilder()->add("news.views > ?", array(0));
		$this->vooliaNewsList->getConditionBuilder()->add("news.isArchived = ?", array(0));
		$this->vooliaNewsList->sqlOrderBy = 'news.views DESC';
		$this->vooliaNewsList->readObjects();

		$this->fetched();
	}

	/**
	 * @see	\wcf\system\dashboard\box\AbstractContentDashboardBox::render()
	 */
	protected function render() {
		if (!count($this->vooliaNewsList)) return '';

		WCF::getTPL()->assign(array(
			'vooliaNewsList' => $this->vooliaNewsList
		));

		return WCF::getTPL()->fetch('dashboardSidebarBoxMostViewedNews', 'news');
	}
}
