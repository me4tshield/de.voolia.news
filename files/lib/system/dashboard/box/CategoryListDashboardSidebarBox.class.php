<?php
namespace news\system\dashboard\box;
use news\data\category\NewsCategoryNodeTree;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Dashboard sidebar box for the news categories.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013-2014 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class CategoryListDashboardSidebarBox extends AbstractSidebarDashboardBox {
	/**
	 * available categories
	 * @var	array<\wcf\data\category\Category>
	 */
	public $categoryList = null;

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		// list all news categories
		$categoryTree = new NewsCategoryNodeTree('de.voolia.news.category');
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);

		$this->fetched();
	}

	/**
	 * @see	\wcf\system\dashboard\box\AbstractSidebarDashboardBox::render()
	 */
	protected function render() {
		if (!count($this->categoryList)) return '';

		WCF::getTPL()->assign(array(
			'categoryList' => $this->categoryList,
			'category' => (!empty($this->page->category) ? $this->page->category : null)
		));

		return WCF::getTPL()->fetch('dashboardSidebarBoxCategoryList', 'news');
	}
}
