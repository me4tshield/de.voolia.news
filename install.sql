-- news
DROP TABLE IF EXISTS news1_news;
CREATE TABLE news1_news (
	newsID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	subject VARCHAR(255) NOT NULL DEFAULT '',
	time INT(10) NOT NULL DEFAULT 0,
	languageID int(10) DEFAULT NULL,
	userID INT(10),
	username VARCHAR(255) NOT NULL DEFAULT '',
	teaser TEXT NOT NULL,
	text LONGTEXT,
	pictureID INT(10) DEFAULT NULL,
	pollID INT(10),
	attachments SMALLINT(5) NOT NULL DEFAULT 0,
	newsUpdates smallint(5) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	isActive TINYINT(1) NOT NULL DEFAULT 0,
	isDeleted TINYINT(1) NOT NULL DEFAULT 0,
	isPublished TINYINT(1) NOT NULL DEFAULT 1,
	publicationDate INT(10) NOT NULL DEFAULT 0,
	isArchived TINYINT(1) NOT NULL DEFAULT 0,
	archivingDate INT(10) NOT NULL DEFAULT 0,
	isHot TINYINT(1) NOT NULL DEFAULT 0,
	isAnnouncement TINYINT(1) NOT NULL DEFAULT 0,
	isCommentable TINYINT(1) NOT NULL DEFAULT 1,
	editCount MEDIUMINT(7) NOT NULL DEFAULT 0,
	editReason VARCHAR(255) NOT NULL DEFAULT '',
	editNoteSuppress TINYINT(1) NOT NULL DEFAULT 0,
	editTime INT(10) NOT NULL DEFAULT 0,
	editUser VARCHAR(255) NOT NULL DEFAULT '',
	deleteTime INT(10) NOT NULL DEFAULT 0,
	deleteReason VARCHAR(255) NOT NULL DEFAULT '',
	views MEDIUMINT(7) NOT NULL DEFAULT 0,
	comments smallint(5) NOT NULL DEFAULT 0,
	cumulativeLikes mediumint(7) NOT NULL DEFAULT 0,

	KEY (time),
	KEY (userID),
	KEY (languageID)
);

-- news picture
DROP TABLE IF EXISTS news1_news_picture;
CREATE TABLE news1_news_picture (
	pictureID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	categoryID INT(10) DEFAULT NULL,
	title VARCHAR(255) NOT NULL DEFAULT '',
	fileExtension VARCHAR(7) NOT NULL DEFAULT '',
	filesize INT(10) NOT NULL DEFAULT 0,
	fileType VARCHAR(255) NOT NULL DEFAULT '',
	fileHash VARCHAR(40) NOT NULL DEFAULT '',
	uploadTime INT(10) NOT NULL DEFAULT 0,

	KEY (uploadTime)
);

-- news source
DROP TABLE IF EXISTS news1_news_source;
CREATE TABLE news1_news_source (
	sourceID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	newsID INT(10) NOT NULL,
	sourceLink VARCHAR(255) NOT NULL DEFAULT '',
	sourceText VARCHAR(255) NOT NULL DEFAULT '',

	KEY newsID (newsID)
);

-- news category
DROP TABLE IF EXISTS news1_news_to_category;
CREATE TABLE news1_news_to_category (
	categoryID INT(10) NOT NULL,
	newsID INT(10) NOT NULL,
	PRIMARY KEY (categoryID, newsID)
);

-- news updates
DROP TABLE IF EXISTS news1_news_update;
CREATE TABLE news1_news_update (
	updateID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	newsID INT(10) NOT NULL,
	time INT(10) NOT NULL DEFAULT 0,
	userID INT(10) DEFAULT NULL,
	username VARCHAR(255) NOT NULL DEFAULT '',
	subject VARCHAR(255) NOT NULL DEFAULT '',
	text LONGTEXT,
	attachments SMALLINT(5) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
);

-- news
ALTER TABLE news1_news ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE news1_news ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE SET NULL;
ALTER TABLE news1_news ADD FOREIGN KEY (pictureID) REFERENCES news1_news_picture (pictureID) ON DELETE SET NULL;
ALTER TABLE news1_news ADD FOREIGN KEY (pollID) REFERENCES wcf1_poll (pollID) ON DELETE SET NULL;

-- news category
ALTER TABLE news1_news_to_category ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE news1_news_to_category ADD FOREIGN KEY (newsID) REFERENCES news1_news (newsID) ON DELETE CASCADE;

-- news picture
ALTER TABLE news1_news_picture ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE SET NULL;

-- news source
ALTER TABLE news1_news_source ADD FOREIGN KEY (newsID) REFERENCES news1_news (newsID) ON DELETE CASCADE;

-- news update
ALTER TABLE news1_news_update ADD FOREIGN KEY (newsID) REFERENCES news1_news (newsID) ON DELETE CASCADE;
ALTER TABLE news1_news_update ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
