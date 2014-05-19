{if $objects|count}
	<ul class="messageList" data-type="de.voolia.news.entry">
		{foreach from=$objects item=news}
		<li>
			<article class="message messageReduced marginTop{if !$news->isActive} messageDisabled{/if}{if $news->isDeleted} messageDeleted{/if}" data-object-id="{$news->newsID}" data-user-id="{$news->userID}">
				<div>
					<section class="messageContent">
						<div>
							<header class="messageHeader">
								{if NEWS_ENABLE_NEWSPICTURE}
									<div class="box32">
										<a href="{$news->getLink()}" class="framed">
											<img src="{@$news->getNewsPicture()->getURL()}" class="newsImage" alt="" />
										</a>
								{/if}

								<div class="messageHeadline">
									<h1>{if $news->newsUpdates}<span class="badge badgeUpdate">{lang}news.entry.newsUpdate.badge{/lang}</span> {/if}<a href="{$news->getLink()}">{$news->subject}</a>{if NEWS_ENABLE_LANGUAGE_FLAG && $news->languageID} {@$news->getLanguageIcon()}{/if}</h1>
									<p>
										<span class="username">{if $news->userID}<a href="{link controller='User' object=$news->getUserProfile()}{/link}" class="userLink" data-user-id="{$news->userID}">{$news->username}</a>{else}{$news->username}{/if}</span>
										<a href="{$news->getLink()}" class="permalink">{@$news->time|time}</a>
										{if $news->getCategories()|count}
											- {implode from=$news->getCategories() item=category}{if $category->isAccessible()}<a href="{link application='news' controller='NewsOverview' object=$category}{/link}">{$category->getTitle()}</a>{/if}{/implode}
										{/if}
										{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && ($news->likes || $news->dislikes)}
											<span class="likesBadge badge jsTooltip {if $news->cumulativeLikes > 0}green{elseif $news->cumulativeLikes < 0}red{/if}" title="{lang likes=$news->likes dislikes=$news->dislikes}wcf.like.tooltip{/lang}">{if $news->cumulativeLikes > 0}+{elseif $news->cumulativeLikes == 0}&plusmn;{/if}{#$news->cumulativeLikes}</span>
										{/if}
									</p>
								</div>

								{if $news->isNew()}
									<p class="newMessageBadge">{lang}wcf.message.new{/lang}{if $news->isHot()} / {lang}news.entry.isHot{/lang}{/if}</p>
								{else if $news->isHot()}
									<p class="newMessageBadge">{lang}news.entry.isHot{/lang}</p>
								{/if}

								{if NEWS_ENABLE_NEWSPICTURE}</div>{/if}

								{event name='messageHeader'}
							</header>

							<div class="messageBody">
								<div>
									{if $news->teaser}
										{@$news->getFormattedTeaser()}
									{elseif NEWS_ENABLE_AUTOMATIC_TEASER}
										{@$news->getFormattedAutomaticTeaser()}
									{else}
										{@$news->getFormattedMessage()}
									{/if}
								</div>

								{event name='messageBody'}

								<div class="messageFooter">
									{if !$news->isPublished}
										<p class="messageFooterNote">{lang}news.entry.delayedPublication{/lang}</p>
									{/if}

									{event name='messageFooterNotes'}
								</div>

								<footer class="messageOptions">
									<nav class="jsMobileNavigation buttonGroupNavigation">
										<ul class="smallButtons buttonGroup">
											<li><a href="{$news->getLink()}" class="button"><span class="icon icon16 icon-arrow-right"></span> <span>{lang}wcf.global.button.readMore{/lang}</span></a></li>
											{if $news->isCommentable()}
												<li><a href="{$news->getLink()}#comments" title="{lang}news.entry.comments{/lang} ({$news->comments})" class="button jsTooltip"><span class="icon icon16 icon-comments"></span> <span>{$news->comments}</span></a></li>
											{/if}
											{event name='messageOptions'}
											<li class="toTopLink"><a href="{$__wcf->getAnchor('top')}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										</ul>
									</nav>
								</footer>
							</div>
						</div>
					</section>
				</div>
			</article>
		</li>
		{/foreach}
	</ul>
{else}
	<p class="info">{lang}news.entry.no.entry{/lang}</p>
{/if}
