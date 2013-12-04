<?php
namespace news\system\user\notification\object\type;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

/**
 * @author	Pascal Bade
 * @copyright	2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsUserNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * @see	\wcf\system\user\notification\object\type\AbstractUserNotificationObjectType::$decoratorClassName
	 */
	protected static $decoratorClassName = 'news\system\user\notification\object\NewsUserNotificationObject';

	/**
	 * @see	\wcf\system\user\notification\object\type\AbstractUserNotificationObjectType::$objectClassName
	 */
	protected static $objectClassName = 'news\data\news\News';

	/**
	 * @see	\wcf\system\user\notification\object\type\AbstractUserNotificationObjectType::$objectListClassName
	 */
	protected static $objectListClassName = 'news\data\news\NewsList';
}
