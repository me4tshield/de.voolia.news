<?php
namespace news\page;
use news\data\news\AccessibleNewsList;
use news\data\news\NewsAction;
use news\data\news\NewsEditor;
use news\data\news\ViewableNews;
use news\system\NEWSCore;
use wcf\page\AbstractPage;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\comment\CommentHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\like\LikeHandler;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\tagging\TagEngine;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows a news entry.
 * 
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsPage extends AbstractPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'news.header.menu.news';

	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_CONTENT_NEWS');

	/**
	 * news id
	 * @var	integer
	 */
	public $newsID = 0;

	/**
	 * news object
	 * @var	\wcf\data\news\ViewableNews
	 */
	public $news = null;

	/**
	 * list of tags
	 * @var	array<\wcf\data\tag\Tag>
	 */
	public $tags = array();

	/**
	 * like data for news
	 * @var	array<\wcf\data\like\object\LikeObject>
	 */
	public $newsLikeData = array();

	/**
	 * comment manager object
	 * @var	\wcf\system\comment\manager\ICommentManager
	 */
	public $commentManager = null;

	/**
	 * list of news comments
	 * @var	\wcf\data\comment\StructuredCommentList
	 */
	public $commentList = null;

	/**
	 * more news list from this category
	 * @var	\news\data\news\AccessibleNewsList
	 */
	public $moreNewsList = null;

	/**
	 * object type id
	 * @var	integer
	 */
	public $objectTypeID = 0;

	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->newsID = intval($_REQUEST['id']);
		$this->news = ViewableNews::getViewableNews($this->newsID);
		if ($this->news === null) {
			throw new IllegalLinkException();
		}

		// check permission for the news
		if (!$this->news->canRead()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// add breadcrumbs
		NEWSCore::getInstance()->setBreadcrumbs();
		if ($this->news->isArchived) {
			WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('news.header.menu.news.archive'), LinkHandler::getInstance()->getLink('NewsArchive', array(
				'application' => 'news'
			))));
		}

		// update news view count
		$this->news->updateVisits();

		// update news visit
		if ($this->news->isNew()) {
			$entryAction = new NewsAction(array($this->news->getDecoratedObject()), 'markAsRead', array(
				'viewableNews' => $this->news
			));
			$entryAction->executeAction();
		}

		// fetch news likes
		if (MODULE_LIKE) {
			$objectType = LikeHandler::getInstance()->getObjectType('de.voolia.news.likeableNews');
			LikeHandler::getInstance()->loadLikeObjects($objectType, array(
					$this->newsID 
			));
			$this->newsLikeData = LikeHandler::getInstance()->getLikeObjects($objectType);
		}

		// get news tags
		if (MODULE_TAGGING) {
			$this->tags = TagEngine::getInstance()->getObjectTags(
				'de.voolia.news.entry',
				$this->news->newsID,
				array(($this->news->languageID === null ? LanguageFactory::getInstance()->getDefaultLanguageID() : ""))
			);
		}

		// get news comments
		if ($this->commentManager === null) {
			$this->objectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.voolia.news.comment');
			$objectType = CommentHandler::getInstance()->getObjectType($this->objectTypeID);
			$this->commentManager = $objectType->getProcessor();
		}

		$this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->objectTypeID, $this->newsID);

		// more news from this category
		$this->moreNewsList = new AccessibleNewsList();
		$this->moreNewsList->enableAttachmentLoading(false);
		$this->moreNewsList->getConditionBuilder()->add("news.newsID IN (SELECT newsID FROM news".WCF_N."_news_to_category WHERE categoryID IN (?))", array($this->news->getCategoryIDs()));
		$this->moreNewsList->sqlLimit = NEWS_DASHBOARD_SIDEBAR_ENTRIES;
		$this->moreNewsList->readObjects();

		// meta tags
		MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->news->subject . ' - ' . WCF::getLanguage()->get(PAGE_TITLE), true);
		MetaTagHandler::getInstance()->addTag('og:url', 'og:url', LinkHandler::getInstance()->getLink('News', array('application' => 'news', 'object' => $this->news)), true);
		MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'article', true);
		MetaTagHandler::getInstance()->addTag('og:description', 'og:description', StringUtil::decodeHTML(StringUtil::stripHTML($this->news->getExcerpt())), true);
		if (NEWS_ENABLE_NEWSPICTURE) {
			MetaTagHandler::getInstance()->addTag('og:image', 'og:image', $this->news->getNewsPicture()->getURL(), true);
		}

		// add tags as keywords
		if (!empty($this->tags)) {
			$keywords = '';
			foreach ($this->tags as $tag) {
				if (!empty($keywords)) $keywords .= ', ';
				$keywords .= $tag->name;
			}
			MetaTagHandler::getInstance()->addTag('keywords', 'keywords', $keywords);
		}

		// quotes
		MessageQuoteManager::getInstance()->initObjects('de.voolia.news.entry', array($this->news->newsID));
	}

	/**
	 * @see	\wcf\page\AbstractPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		MessageQuoteManager::getInstance()->assignVariables();

		// configuration for dashboard boxes
		DashboardHandler::getInstance()->loadBoxes('de.voolia.news.NewsPage', $this);

		WCF::getTPL()->assign(array(
			'allowSpidersToIndexThisPage' => true,
			'attachmentList' => $this->news->getAttachments(),
			'commentCanAdd' => WCF::getSession()->getPermission('user.news.canWriteComment'),
			'commentList' => $this->commentList,
			'commentObjectTypeID' => $this->objectTypeID,
			'lastCommentTime' => $this->commentList->getMinCommentTime(),
			'likeData' => (MODULE_LIKE ? $this->commentList->getLikeData() : array()),
			'news' => $this->news,
			'newsLikeData' => $this->newsLikeData,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.voolia.news.NewsPage'),
			'sidebarName' => 'de.voolia.news.NewsPage',
			'tags' => $this->tags,
			'moreNewsList' => $this->moreNewsList
		));
	}

	/**
	 * @see	\wcf\page\ITrackablePage::getObjectType()
	 */
	public function getObjectType() {
		return 'de.voolia.news';
	}

	/**
	 * @see	\wcf\page\ITrackablePage::getObjectID()
	 */
	public function getObjectID() {
		return $this->newsID;
	}
}
