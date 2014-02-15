<?php
namespace news\system\message\quote;
use news\data\news\NewsList;
use wcf\system\message\quote\AbstractMessageQuoteHandler;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\message\quote\QuotedMessage;

/**
 * IMessageQuoteHandler implementation for news entries.
 *
 * @author	Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */
class NewsMessageQuoteHandler extends AbstractMessageQuoteHandler {
	/**
	 * @see	\wcf\system\message\quote\AbstractMessageQuoteHandler::getMessages()
	 */
	protected function getMessages(array $data) {
		// read news entries
		$newsList = new NewsList();
		$newsList->getConditionBuilder()->add('news.newsID IN (?)', array(array_keys($data)));
		$newsList->readObjects();

		$quotedMessages = $validNewsIDs = array();

		// create QuotedMessage objects
		foreach ($newsList->getObjects() as $news) {
			$validNewsIDs[] = $news->newsID;
			$message = new QuotedMessage($news);

			foreach (array_keys($data[$news->newsID]) as $quoteID) {
				$message->addQuote(
					$quoteID,
					MessageQuoteManager::getInstance()->getQuote($quoteID, false),	// single quote or excerpt
					MessageQuoteManager::getInstance()->getQuote($quoteID, true)	// same as above or full quote
				);
			}

			$quotedMessages[] = $message;
		}

		// check for orphaned quotes
		if (count($validNewsIDs) != count($data)) {
			$orphanedQuoteIDs = array();
			foreach ($data as $newsID => $quoteIDs) {
				if (!in_array($newsID, $validNewsIDs)) {
					foreach (array_keys($quoteIDs) as $quoteID) {
						$orphanedQuoteIDs[] = $quoteID;
					}
				}
			}

			MessageQuoteManager::getInstance()->removeOrphanedQuotes($orphanedQuoteIDs);
		}

		return $quotedMessages;
	}
}
