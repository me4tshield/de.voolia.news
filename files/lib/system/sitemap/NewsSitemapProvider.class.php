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
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsSitemapProvider implements ISitemapProvider {
	/**
	 * @see	\wcf\system\sitemap\ISitemapProvider::getTemplate()
	 */
	public function getTemplate() {
		// get all accessible news categories
		$categoryList = new NewsCategoryNodeTree('de.voolia.news.category');

		WCF::getTPL()->assign(array(
			'categoryNodeList' => $categoryList->getIterator()
		));

		// init template
		return WCF::getTPL()->fetch('newsSitemap', 'news');
	}
}
