{include file='documentHeader'}

<head>
	<title>{lang}news.archive.title{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}

	{if $__wcf->getUser()->userID}
		<link rel="alternate" type="application/rss+xml" href="{link application='news' controller='NewsFeed' appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}" title="{lang}wcf.global.button.rss{/lang}" />
	{else}
		<link rel="alternate" type="application/rss+xml" href="{link application='news' controller='NewsFeed' appendSession=false}{/link}" title="{lang}wcf.global.button.rss{/lang}" />
	{/if}
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}
	{include file='newsSidebarCategoryList' application='news'}

	{if NEWS_ENABLE_LETTER_SORT}
		{include file='newsSidebarLetterList' application='news'}
	{/if}

	{event name='boxes'}

	{@$__boxSidebar}
{/capture}

{capture assign='headerNavigation'}
	{if $category}
		<li><a rel="alternate" href="{if $__wcf->getUser()->userID}{link application='news' controller='NewsFeed' id=$category->categoryID appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}{else}{link application='news' controller='NewsFeed' id=$category->categoryID appendSession=false}{/link}{/if}" title="{lang}wcf.global.button.rss{/lang}" class="jsTooltip"><span class="icon icon16 icon-rss"></span> <span class="invisible">{lang}wcf.global.button.rss{/lang}</span></a></li>
	{else}
		<li><a rel="alternate" href="{if $__wcf->getUser()->userID}{link application='news' controller='NewsFeed' appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}{else}{link application='news' controller='NewsFeed' appendSession=false}{/link}{/if}" title="{lang}wcf.global.button.rss{/lang}" class="jsTooltip"><span class="icon icon16 icon-rss"></span> <span class="invisible">{lang}wcf.global.button.rss{/lang}</span></a></li>
	{/if}
{/capture}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
	<h1>{lang}news.archive.title{/lang}</h1>
	<h2>{lang}news.archive.description{/lang}</h2>
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='news' controller='NewsArchive' object=$category link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}

	{event name='contentNavigationButtonsTop'}
</div>

<div class="marginTop tabularBox tabularBoxTitle messageGroupList">
	<header>
		<h2>{lang}news.archive.title{/lang} <span class="badge badgeInverse">{$stats[news]} {lang}news.entry.title{/lang}</span></h2>
	</header>

		<table class="table">
			<thead>
				<tr>
					<th class="columnTitle columnSubject{if $sortField == 'subject'} active {@$sortOrder}{/if}"><a href="{link application='news' controller='NewsArchive'}{if $filter}filter={@$filter}&{/if}pageNo={@$pageNo}&sortField=subject&sortOrder={if $sortField == 'subject' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}news.archive.box.title{/lang}</a></th>
					<th class="columnText columnTime{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link application='news' controller='NewsArchive'}{if $filter}filter={@$filter}&{/if}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}news.archive.box.time{/lang}</a></th>
				</tr>
			</thead>

			<tbody>
				{foreach from=$objects item=news}
					<tr class="newsArchive{if !$news->isActive} messageDisabled{/if}{if $news->isDeleted} messageDeleted{/if}">
						<td class="columnText columnSubject">
							<h3>
								<p>{if $news->time > TIME_NOW}<span class="icon icon16 icon-time jsTooltip" title="{lang}news.entry.delayed{/lang}"></span> {/if}<a href="{link application='news' controller='News' object=$news}{/link}" class="newsPreview" data-news-id="{@$news->newsID}" title="{$news->subject}">{$news->subject}</a></p>
								<p><small>{lang}news.sidebar.entry.general.category{/lang}: {implode from=$news->getCategories() item=category}{if $category->isAccessible()}<a href="{link application='news' controller='NewsArchive' object=$category}{/link}" class="jsTooltip" title="">{$category->getTitle()}</a>{/if}{/implode}</small></p>
							</h3>
						</td>
						<td class="columnDigits columnTime">{@$news->time|time}</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="3">
							{lang}news.entry.no.entry{/lang}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

<div class="contentNavigation">
	{@$pagesLinks}

	{event name='contentNavigationButtonsBottom'}
</div>

{hascontent}
<div class="container marginTop">
	<ul class="containerList infoBoxList">
		{content}
			{if NEWS_INDEX_ENABLE_USERS_ONLINE_LIST}
				{include file='usersOnlineInfoBox'}
			{/if}

			{if NEWS_INDEX_ENABLE_STATISTICS}
				<li class="box32 statsInfoBox">
					<span class="icon icon32 icon-bar-chart"></span>

					<div>
						<div class="containerHeadline">
							<h3>{lang}news.index.statistics.title{/lang}</h3>
							<p>{lang}news.index.statistics.data{/lang}</p>
						</div>
					</div>
				</li>
			{/if}

			{event name='infoBoxes'}
		{/content}
	</ul>
</div>
{/hascontent}

{include file='footer'}
</body>
</html>
