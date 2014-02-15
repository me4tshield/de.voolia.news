<?php
namespace news\system\sitemap;
use news\data\category\NewsCategoryNodeTree;
use wcf\system\sitemap\ISitemapProvider;
use wcf\system\WCF;

/**
 * Provides a news-sitemap.
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsSitemapProvider implements ISitemapProvider {
	/**
	 * @see	\wcf\system\sitemap\ISitemapProvider::getTemplate()
	 */
	public function getTemplate() {
		$categoryTree = new NewsCategoryNodeTree('de.voolia.news.category');
		$categoryList = $categoryTree->getIterator();
		$categoryList->setMaxDepth(1);

		WCF::getTPL()->assign(array(
			'categoryList' => $categoryList
		));

		// init template
		return WCF::getTPL()->fetch('sitemap', 'news');
	}
}
