<?php
namespace news\system\comment\manager;
use news\data\news\News;
use news\data\news\NewsEditor;
use wcf\data\comment\response\CommentResponse;
use wcf\data\comment\Comment;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Implementation of news comments
 * 
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsCommentManager extends AbstractCommentManager {
	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionAdd
	 */
	protected $permissionAdd = 'user.news.canWriteComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionCanModerate
	 */
	protected $permissionCanModerate = 'mod.news.canModerateNews';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionDelete
	 */
	protected $permissionDelete = 'user.news.canDeleteOwnComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionEdit
	 */
	protected $permissionEdit = 'user.news.canEditOwnComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionModDelete
	 */
	protected $permissionModDelete = 'mod.news.canDeleteComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionModEdit
	 */
	protected $permissionModEdit = 'mod.news.canEditComment';

	/**
	 * news object
	 * @var	\wcf\data\news\ViewableNews
	 */
	public $news = null;

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::getLink()
	 */
	public function getLink($objectTypeID, $objectID) {
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'news',
			'id' => $objectID
		));
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::getTitle()
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) return WCF::getLanguage()->get('news.entry.commentResponse');

		return WCF::getLanguage()->getDynamicVariable('news.entry.comment');
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::isAccessible()
	 */
	public function isAccessible($objectID, $validateWritePermission = false) {
		// Make sure, that the current news is accessible
		$news = new News($objectID);
		if (!$news->newsID || !$news->canRead()) {
			return false;
		}

		return true;
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::updateCounter()
	 */
	public function updateCounter($objectID, $value) {
		$news = new News($objectID);
		$editor = new NewsEditor($news);

		// update counter for news comments
		$editor->updateCounters(array(
			'comments' => $value
		));
	}
}
