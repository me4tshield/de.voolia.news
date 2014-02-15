<?php
use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\WCF;

/**
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2012-2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
$sql = "UPDATE	wcf".WCF_N."_option
	SET	optionValue = ?
	WHERE	optionName = ?";
$optionUpdateStatement = WCF::getDB()->prepareStatement($sql);

// set default news category
CategoryEditor::create(array(
	'objectTypeID' => ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.category', 'de.voolia.news.category'),
	'title' => 'Default Category',
	'description' => 'Description for Default Category',
	'time' => TIME_NOW
));

// enable the news-bbcode by default
$sql = "UPDATE	wcf".WCF_N."_user_group_option_value
        SET	optionValue = CONCAT(REPLACE(optionValue, ',news', ''), ',news')
        WHERE	optionID IN (
                	SELECT	optionID
			FROM	wcf".WCF_N."_user_group_option
			WHERE	optionName LIKE '%.allowedBBCodes'
		)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();

// set default page title
if (!defined('PAGE_TITLE') || !PAGE_TITLE) {
	$optionUpdateStatement->execute(array('News-System', 'page_title'));
}

// default dashboard settings
DashboardHandler::setDefaultValues('de.voolia.news.NewsOverviewPage', array(
	'de.voolia.news.tagCloud' => 1
));
DashboardHandler::setDefaultValues('de.voolia.news.NewsArchivePage', array(
	'de.voolia.news.tagCloud' => 1
));

// set install date
$optionUpdateStatement->execute(array(TIME_NOW, 'news_install_date'));
