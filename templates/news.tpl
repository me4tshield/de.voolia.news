{include file='documentHeader'}

<head>
	<title>{$news->getTitle()} - {lang}news.entry.title{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}

	{if NEWS_ENABLE_LOCATION && $news->location}
		{include file='googleMapsJavaScript'}
	{/if}

	<script data-relocate="true" type="text/javascript">
		//<![CDATA[
			$(function() {
				WCF.Language.addObject({
					'wcf.message.share': '{lang}wcf.message.share{/lang}',
					'wcf.message.share.facebook': '{lang}wcf.message.share.facebook{/lang}',
					'wcf.message.share.google': '{lang}wcf.message.share.google{/lang}',
					'wcf.message.share.permalink': '{lang}wcf.message.share.permalink{/lang}',
					'wcf.message.share.permalink.bbcode': '{lang}wcf.message.share.permalink.bbcode{/lang}',
					'wcf.message.share.permalink.html': '{lang}wcf.message.share.permalink.html{/lang}',
					'wcf.message.share.reddit': '{lang}wcf.message.share.reddit{/lang}',
					'wcf.message.share.twitter': '{lang}wcf.message.share.twitter{/lang}',
					'wcf.moderation.report.reportContent': '{lang}wcf.moderation.report.reportContent{/lang}',
					'wcf.moderation.report.success': '{lang}wcf.moderation.report.success{/lang}',
					'news.entry.bbcode.dialog.compact': '{lang}news.entry.bbcode.dialog.compact{/lang}',
					'news.entry.bbcode.dialog.detailed': '{lang}news.entry.bbcode.dialog.detailed{/lang}',
					'news.entry.button.dropdown.activateComments': '{lang}news.entry.button.dropdown.activateComments{/lang}',
					'news.entry.button.dropdown.deactivateComments': '{lang}news.entry.button.dropdown.deactivateComments{/lang}',
					'news.entry.button.dropdown.delete': '{lang}news.entry.button.dropdown.delete{/lang}',
					'news.entry.button.dropdown.restore': '{lang}news.entry.button.dropdown.restore{/lang}',
					'news.entry.button.dropdown.update': '{lang}news.entry.button.dropdown.update{/lang}',
					'news.entry.delete.sure': '{lang}news.entry.delete.sure{/lang}',
					'news.entry.trash.confirmMessage': '{lang}news.entry.trash.confirmMessage{/lang}',
					'news.entry.trash.reason': '{lang}news.entry.trash.reason{/lang}'
				});

				{if $news->canManageNews()}
					new WCF.Action.Delete('news\\data\\news\\update\\NewsUpdateAction', '.newsUpdate');
					var $inlineEditor = new News.InlineEditor('.newsMessage', '{link application='news' controller='NewsOverview'}{/link}', '{link application='news' controller='NewsUpdateAdd' id=$news->newsID}{/link}');
				{/if}

				{include file='__messageQuoteManager' wysiwygSelector='text' supportPaste=false}
				{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}new News.Like({if $__wcf->getUser()->userID && $__wcf->getSession()->getPermission('user.like.canLike')}1{else}0{/if}, {@LIKE_ENABLE_DISLIKE}, {@LIKE_SHOW_SUMMARY}, {@LIKE_ALLOW_FOR_OWN_CONTENT});{/if}
				new News.QuoteHandler($quoteManager);
				new WCF.Message.Share.Content();
				new WCF.Moderation.Report.Content('de.voolia.news.entry', '.jsReportNews');
				new WCF.Moderation.Report.Content('de.voolia.news.entry.update', '.jsReportNewsUpdate');

				// News BBCode
				var $jsNewsBBCode = $('<div id="jsNewsBBCode"><fieldset><legend><label>{lang}news.entry.bbcode.dialog.compact{/lang}</label></legend><input type="text" class="long" readonly="readonly" value="[news={$news->newsID}][/news]" /></fieldset><fieldset><legend><label>{lang}news.entry.bbcode.dialog.detailed{/lang}</label></legend><input type="text" class="long" readonly="readonly" value="[news={$news->newsID},meta][/news]" /></fieldset></div>');
				$(".jsNewsBBCode").on("click", function() {
					$jsNewsBBCode.wcfDialog({ "title": "{lang}wcf.message.share.permalink.bbcode{/lang}"});
				});

				{if NEWS_ENABLE_LOCATION && $news->location}
					var $map = new WCF.Location.GoogleMaps.Map('newsMap');
					WCF.Location.GoogleMaps.Util.focusMarker($map.addMarker({@$news->latitude}, {@$news->longitude}, '{$news->subject|encodeJS}'));
				{/if}
			});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}
	{if NEWS_ENABLE_ENTRY_AUTHOR_INFOS}
		{include file='authorSidebarBox' userProfile=$news->getUserProfile()}
	{/if}

	<fieldset>
		<legend>{lang}news.sidebar.entry.general.title{/lang}</legend>

		<dl class="plain inlineDataList">
			{if NEWS_ENABLE_LANGUAGE_FLAG && $news->languageID}
				<dt>{lang}wcf.user.language{/lang}</dt>
				<dd>{$news->getLanguage()->languageName}</dd>
			{/if}

			<dt>{lang}news.sidebar.entry.general.views{/lang}</dt>
			<dd>{$news->views}</dd>
		</dl>
	</fieldset>

	{if $news->getCategories()|count}
		<fieldset>
			<legend>{lang}news.sidebar.entry.general.category{/lang}</legend>

			<ul>
				{foreach from=$news->getCategories() item=category}
					{if $category->isAccessible()}
						<li><a href="{link application='news' controller='NewsOverview' object=$category}{/link}" class="jsTooltip" title="">{$category->getTitle()}</a></li>
					{/if}
				{/foreach}
			</ul>
		</fieldset>
	{/if}

	{if $tags|count}
		<fieldset>
			<legend>{lang}wcf.tagging.tags{/lang}</legend>

			<ul class="tagList">
				{foreach from=$tags item=tag}
					<li><a href="{link controller='Tagged' object=$tag}objectType=de.voolia.news.entry{/link}" class="badge jsTooltip tag" title="{lang}wcf.tagging.taggedObjects.de.voolia.news.entry{/lang}">{$tag->name}</a></li>
				{/foreach}
			</ul>
		</fieldset>
	{/if}

	{if NEWS_ENABLE_LOCATION && $news->location}
		<fieldset>
			<legend>{lang}news.sidebar.entry.general.map{/lang}</legend>
			{if $news->location}
				<small>{$news->location}</small>
			{/if}
			
			<div class="sidebarGoogleMap" id="newsMap"></div>
		</fieldset>
	{/if}

	{if NEWS_ENABLE_ENTRY_SIMILAR_NEWS}
		<fieldset>
			<legend>{lang}news.sidebar.entry.similar.title{/lang}</legend>

			<ul>
				{foreach from=$moreNewsList item=moreNews}
					<li{if NEWS_ENABLE_NEWSPICTURE} class="box24"{/if}>
						{if NEWS_ENABLE_NEWSPICTURE}
							<a href="{$moreNews->getLink()}" class="framed"><img src="{@$moreNews->getNewsPicture()->getURL()}" alt="" class="newsImageSidebar" /></a>
						{/if}

						<div class="sidebarBoxHeadline">
							<h3><a href="{$moreNews->getLink()}" class="newsPreview" data-news-id="{@$moreNews->newsID}" title="{$moreNews->getTitle()}">{$moreNews->getTitle()}</a></h3>
							<small>{$moreNews->views} {lang}news.sidebar.entry.general.views{/lang} {* TODO: Add views & comments *}</small>
						</div>
					</li>
				{/foreach}
			</ul>
		</fieldset>
	{/if}

	{@$__boxSidebar}

	{event name='boxes'}
{/capture}

{include file='header' sidebarOrientation='right'}

{include file='userNotice'}

{if !$news->isPublished}
	<p class="info">{lang}news.entry.delayedPublication{/lang}</p>
{/if}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $news->canManageNews()}
						<li><a href="{link application='news' controller='NewsEdit' id=$news->newsID}{/link}" class="button jsButtonNewsInlineEditor" title="{lang}wcf.global.button.edit{/lang}"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>
					{/if}

					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{assign var='objectID' value=$news->newsID}
<ul class="messageList">
	<li>
		<article class="newsMessage message messageReduced marginTop{if !$news->isActive} messageDisabled{/if}{if $news->isDeleted} messageDeleted{/if}" data-object-id="{$news->newsID}" data-object-type="de.voolia.news.likeableNews" data-user-id="{@$news->userID}" data-like-liked="{if $newsLikeData[$news->newsID]|isset}{@$newsLikeData[$news->newsID]->liked}{/if}" data-like-likes="{if $newsLikeData[$news->newsID]|isset}{@$newsLikeData[$news->newsID]->likes}{else}0{/if}" data-like-dislikes="{if $newsLikeData[$news->newsID]|isset}{@$newsLikeData[$news->newsID]->dislikes}{else}0{/if}" data-like-users='{if $newsLikeData[$news->newsID]|isset}{ {implode from=$newsLikeData[$news->newsID]->getUsers() item=likeUser}"{@$likeUser->userID}": { "username": "{$likeUser->username|encodeJSON}" }{/implode} }{else}{ }{/if}' data-is-active="{@$news->isActive}" data-is-commentable="{@$news->isCommentable}" data-is-deleted="{@$news->isDeleted}" data-can-manage-comments="{if $news->canManageComments()}true{else}false{/if}">
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
								<h1><a href="{$news->getLink()}">{$news->subject}</a></h1>
								<p>
									<span class="username">{if $news->userID}<a href="{link controller='User' object=$news->getUserProfile()}{/link}" class="userLink" data-user-id="{$news->userID}">{$news->username}</a>{else}{$news->username}{/if}</span>
									<a href="{$news->getLink()}" class="permalink">{@$news->time|time}</a>
								</p>
							</div>

							{if $news->isHot()}<p class="newMessageBadge">{lang}news.entry.isHot{/lang}</p>{/if}

							{if NEWS_ENABLE_NEWSPICTURE}</div>{/if}

							{event name='messageHeader'}
						</header>

						<div class="messageBody">
							<div>
								<div class="messageText">
									{if $news->teaser && NEWS_ENABLE_TEASER_ON_NEWS}
										<div class="newsTeaser">
											{@$news->getFormattedTeaser()}
										</div>
									{/if}

									{@$news->getFormattedMessage()}

									{if $news->pollID|isset}
										<div class="marginTop">
											{include file='poll' poll=$news->getPoll()}
										</div>
									{/if}

									{event name='messageText'}
								</div>
							</div>

							{include file='attachments'}

							{if NEWS_ENTRY_ENABLE_SOURCES && $news->getSources()|count}
								<div class="newsSourceList">
									<fieldset>
										<legend>{lang}news.entry.sources{/lang}</legend>
										<ol class="nativeList">
											{foreach from=$news->getSources() item=source}
												<li id="source{@$source->sourceID}">
													<ul class="dataList">
														{if $source->sourceLink && NEWS_ENTRY_SOURCES_DISPLAYOPTION_COMPACTVIEW}
															<li><strong><a href="{$source->getLink()}">{if $source->sourceText}{$source->sourceText}{else}{$source->getLink()}{/if}</a></strong></li>
														{else}
															{if $source->sourceText}
																<li><strong>{$source->sourceText}</strong></li>
															{/if}
															{if $source->sourceLink}
																<li><a href="{$source->getLink()}">{$source->getLink()}</a></li>
															{/if}
														{/if}
													</ul>
												</li>
											{/foreach}
										</ol>
									</fieldset>
								</div>
							{/if}

							<div class="messageFooter">
								{if NEWS_SHOW_EDITNOTE && $news->editCount && $__wcf->getSession()->getPermission('user.news.canSeeEditNote') && !$news->editNoteSuppress}
									<p class="messageFooterNote">{lang}news.entry.editNote{/lang}</p>
								{/if}

								{event name='messageFooterNotes'}
							</div>

							<footer class="messageOptions">
								<nav class="jsMobileNavigation buttonGroupNavigation">
									<ul class="smallButtons newsSmallButtons buttonGroup">
										<li class="jsReportNews jsOnly" data-object-id="{@$news->newsID}"><a title="{lang}news.entry.button.reportIssue{/lang}" class="button jsTooltip"><span class="icon icon16 icon-bug"></span> <span class="invisible">{lang}news.entry.button.reportIssue{/lang}</span></a></li>
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
</ul>

{if $news->newsUpdates}
	<header id="updates" class="boxHeadline boxSubHeadline">
		<h2>{lang}news.entry.updates{/lang} <span class="badge">{$news->newsUpdates}</h2>
	</header>

	<ul class="messageList">
		{foreach from=$news->getNewsUpdateList() item=update}
			<li id="update{@$update->updateID}" class="newsUpdate" data-object-id="{@$update->updateID}">
				<article class="updateMessage message messageReduced marginTop">
					<div>
						<section class="messageContent">
							<div>
								<header class="messageHeader">
									<div class="messageHeadline">
										<h1>{$update->subject}</h1>
										<p>
											<span class="username"><a href="{link controller='User' object=$update->getUserProfile()}{/link}" class="userLink" data-user-id="{$update->userID}">{$update->getUsername()}</a></span>
											<a href="{link application='news' controller='News' object=$news}#update{@$update->updateID}{/link}" class="permalink">{@$update->getTime()|time}</a>
										</p>
									</div>

									{event name='updateMessageHeader'}
								</header>

								<div class="messageBody">
									<div>
										<div class="messageText">
											{@$update->getFormattedMessage()}

											{event name='updateMessageText'}
										</div>
									</div>

									<div class="messageFooter">
										{event name='updateMessageFooterNotes'}
									</div>

									<footer class="messageOptions">
										<nav class="jsMobileNavigation buttonGroupNavigation">
											<ul class="smallButtons buttonGroup">
												{event name='updateMessageOptions'}
												{if $news->canManageNews()}
													<li><a href="{link application='news' controller='NewsUpdateEdit' id=$update->updateID}{/link}" title="{lang}news.entry.update.edit.title{/lang}" class="button jsTooltip"><span class="icon icon16 icon-pencil"></span> <span class="invisible">{lang}news.entry.update.edit.title{/lang}</span></a></li>
													<li class="jsDeleteButton jsOnly" data-object-id="{@$update->updateID}"><a title="{lang}news.entry.update.delete.title{/lang}" class="button jsTooltip"><span class="icon icon16 icon-remove"></span> <span class="invisible">{lang}news.entry.update.delete.title{/lang}</span></a></li>
												{/if}
												<li class="jsReportNewsUpdate jsOnly" data-object-id="{@$update->updateID}"><a title="{lang}news.entry.button.reportIssue{/lang}" class="button jsTooltip"><span class="icon icon16 icon-bug"></span> <span class="invisible">{lang}news.entry.button.reportIssue{/lang}</span></a></li>
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
{/if}

<div class="contentNavigation">
	<nav>
		{if NEWS_ENABLE_PREVIOUS_AND_NEXT_NEWS_LIST}
			{hascontent}
				<ul style="float: left;">
					{content}
						{foreach from=$news->getPreviousNews() item=previous}
							<li><a href="{link application='news' controller='News' object=$previous}{/link}" class="button small newsPreview" data-news-id="{@$previous->newsID}" title="{$previous->subject}"><span>{lang}news.entry.previousNewsItem{/lang}</span></a></li>
						{/foreach}
						{foreach from=$news->getNextNews() item=next}
							<li><a href="{link application='news' controller='News' object=$next}{/link}" class="button small newsPreview" data-news-id="{@$next->newsID}" title="{$next->subject}"><span>{lang}news.entry.nextNewsItem{/lang}</span></a></li>
						{/foreach}
					{/content}
				</ul>
			{/hascontent}
		{/if}
		<ul>
			<li><a href="{link application='news' controller='News' appendSession=false object=$news}{/link}" class="button jsButtonShare jsOnly" title="{lang}wcf.message.share{/lang}" data-link-title="{$news->subject}"><span class="icon icon16 icon-link"></span> <span>{lang}wcf.message.share{/lang}</span></a></li>
			<li><a class="button jsNewsBBCode jsOnly"><span class="icon icon16 icon-copy"></span> <span>{lang}wcf.message.share.permalink.bbcode{/lang}</span></a></li>
			{event name='contentNavigationButtonsBottom'}
		</ul>
	</nav>
	{if ENABLE_SHARE_BUTTONS}
		{include file='shareButtons'}
	{/if}
</div>

{if $news->isCommentable()}
	<header id="comments" class="boxHeadline boxSubHeadline">
		<h2>{lang}news.entry.comments{/lang} <span class="badge">{#$news->comments}</span></h2>
	</header>

	{include file='__commentJavaScript' commentContainerID='newsCommentList'}

	<div class="container containerList marginTop">
		{if $commentCanAdd}
			<ul id="newsCommentList" class="commentList containerList" data-can-add="true" data-object-id="{@$news->newsID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
				{include file='commentList'}
			</ul>
		{else}
			{hascontent}
				<ul id="newsCommentList" class="commentList containerList" data-can-add="false" data-object-id="{@$news->newsID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
					{content}
						{include file='commentList'}
					{/content}
				</ul>
			{hascontentelse}
				<div class="containerPadding">
					{lang}news.entry.comments.noEntry{/lang}
				</div>
			{/hascontent}
		{/if}
	</div>
{/if}

{include file='footer'}

</body>
</html>
