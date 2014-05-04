<?php
namespace news\system\attachment;
use news\data\news\News;
use news\data\news\NewsList;
use wcf\system\attachment\AbstractAttachmentObjectType;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Attachment object type implementation for news entries.
 * 
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsAttachmentObjectType extends AbstractAttachmentObjectType {
	/**
	 * cached news objects
	 * @var	array<\news\data\news\News>
	 */
	protected $cachedObjects = array();

	/**
	 * @see	\wcf\system\attachment\IAttachmentObjectType::cacheObjects()
	 */
	public function cacheObjects(array $objectIDs) {
		$newsList = new NewsList();
		$newsList->setObjectIDs($objectIDs);
		$newsList->readObjects();
		$this->cachedObjects = $newsList->getObjects();
	}

	/**
	 * @see	\wcf\system\attachment\IAttachmentObjectType::getAllowedExtensions()
	 */
	public function getAllowedExtensions() {
		return ArrayUtil::trim(explode("\n", WCF::getSession()->getPermission('user.news.allowedAttachmentExtensions')));
	}

	/**
	 * @see	\wcf\system\attachment\IAttachmentObjectType::getMaxCount()
	 */
	public function getMaxCount() {
		return WCF::getSession()->getPermission('user.news.maxAttachmentCount');
	}

	/**
	 * @see	\wcf\system\attachment\IAttachmentObjectType::getObject()
	 */
	public function getObject($objectID) {
		if (isset($this->cachedObjects[$objectID])) return $this->cachedObjects[$objectID];
		return null;
	}

	/**
	 * @see	\wcf\system\attachment\IAttachmentObjectType::canDownload()
	 */
	public function canDownload($objectID) {
		if ($objectID) {
			$news = new News($objectID);
			if ($news->canRead()) return true;
		}

		return false;
	}

	/**
	 * @see	\wcf\system\attachment\IAttachmentObjectType::canUpload()
	 */
	public function canUpload($objectID, $parentObjectID = 0) {
		return WCF::getSession()->getPermission('user.news.canUploadAttachment');
	}

	/**
	 * @see	\wcf\system\attachment\IAttachmentObjectType::canDelete()
	 */
	public function canDelete($objectID) {
		if ($objectID) {
			$news = new News($objectID);
			return $news->isEditable();
		}

		return false;
	}
}
