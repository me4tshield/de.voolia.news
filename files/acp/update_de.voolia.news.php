<?php
use wcf\system\WCF;

/**
 * @author	Pascal Bade <mail@voolia.de>
 * @copyright	2012-2013 voolia.de
 * @license	Creative Commons CC-BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
// delete obsolete object types
$sql = "DELETE FROM	wcf".WCF_N."_object_type
	WHERE		objectType IN (?,?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array(
	'de.voolia.news.exporter.yaycom1x',
	'de.voolia.news.exporter.cnews1x'
));

// remove obsolete dashboard boxes from content area
$sql = "SELECT boxID FROM wcf".WCF_N."_dashboard_box WHERE boxName IN (?,?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array(
	'de.voolia.news.hotNewsContent',
	'de.voolia.news.latestNewsContent'
));
while ($row = $statement->fetchArray()) {
	$sql = "DELETE FROM	wcf".WCF_N."_dashboard_option
		WHERE		boxID = ?";
	$boxStatement = WCF::getDB()->prepareStatement($sql);
	$boxStatement->execute(array(
		$row['boxID']
	));
}
