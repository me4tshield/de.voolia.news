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
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class MostViewedNewsDashboardSidebarBox extends AbstractSidebarDashboardBox {
	/**
	 * most viewed news entries list
	 * @var	\news\data\news\AccessibleNewsList
	 */
	public $newsList = null;

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		$this->newsList = new AccessibleNewsList();
		$this->newsList->sqlLimit = NEWS_DASHBOARD_SIDEBAR_ENTRIES;
		$this->newsList->getConditionBuilder()->add("news.views > ?", array(0));
		$this->newsList->getConditionBuilder()->add("news.isArchived = ?", array(0));
		$this->newsList->sqlOrderBy = 'news.views DESC';
		$this->newsList->readObjects();

		$this->fetched();
	}

	/**
	 * @see	\wcf\system\dashboard\box\AbstractContentDashboardBox::render()
	 */
	protected function render() {
		if (!count($this->newsList)) return '';

		WCF::getTPL()->assign(array(
			'newsList' => $this->newsList
		));

		return WCF::getTPL()->fetch('dashboardSidebarBoxMostViewedNews', 'news');
	}
}
