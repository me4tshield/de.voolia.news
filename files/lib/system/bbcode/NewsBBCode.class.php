<?php
namespace news\system\bbcode;
use news\data\news\ViewableNews;
use wcf\system\bbcode\AbstractBBCode;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Parses the [news] bbcode tag.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsBBCode extends AbstractBBCode {
	/**
	 * @see	\wcf\system\bbcode\IBBCode::getParsedTag()
	 */
	public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser) {
		// get news id from bbcode
		if (isset($openingTag['attributes'][0])) {
			$newsID = $openingTag['attributes'][0];
		}

		$displayMode = 'link';
		if ($parser->getOutputType() == 'text/html' && isset($openingTag['attributes'][1])) {
			$displayMode = $openingTag['attributes'][1];
		}

		// get news
		$news = ViewableNews::getViewableNews($newsID);
		if ($news === null) return '';

		switch ($displayMode) {
			case 'meta':
				WCF::getTPL()->assign(array(
					'_news' => $news
				));

				return WCF::getTPL()->fetch('newsBBCodeTag', 'news');
			break;

			default:
				return '<a href="'. LinkHandler::getInstance()->getLink('news', array('application' => 'news','object' => $news)) .'">'. (($content) ?: $news->subject) .'</a>';
		}
	}
}
