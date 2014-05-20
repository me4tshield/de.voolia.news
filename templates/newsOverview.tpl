{include file='documentHeader'}

<head>
	<title>{if $__wcf->getPageMenu()->getLandingPage()->menuItem != 'news.header.menu.news'}{lang}news.index.overview{/lang} - {/if}{PAGE_TITLE|language}</title>

	{include file='headInclude'}

	{if $__wcf->getUser()->userID}
		<link rel="alternate" type="application/rss+xml" href="{link application='news' controller='NewsFeed' appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}" title="{lang}wcf.global.button.rss{/lang}" />
	{else}
		<link rel="alternate" type="application/rss+xml" href="{link application='news' controller='NewsFeed' appendSession=false}{/link}" title="{lang}wcf.global.button.rss{/lang}" />
	{/if}

	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			WCF.Language.addObject({
				'wcf.user.objectWatch.manageSubscription': '{lang}wcf.user.objectWatch.manageSubscription{/lang}'
			});

			new News.Category.MarkAllAsRead();
			new WCF.User.ObjectWatch.Subscribe();

			{if $__wcf->session->getPermission('mod.news.canEditNews')}
				WCF.Clipboard.init('news\\page\\NewsOverviewPage', {@$hasMarkedItems});
			{/if}
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}
	{if NEWS_ENABLE_SIDEBAR_CATEGORIES}
		{include file='newsSidebarCategoryList' application='news'}
	{/if}

	{if NEWS_ENABLE_LETTER_SORT}
		{include file='newsSidebarLetterList' application='news'}
	{/if}

	{event name='boxes'}

	{@$__boxSidebar}
{/capture}

{capture assign='headerNavigation'}
	{if $category}
		<li><a rel="alternate" href="{if $__wcf->getUser()->userID}{link application='news' controller='NewsFeed' id=$category->categoryID appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}{else}{link application='news' controller='NewsFeed' id=$category->categoryID appendSession=false}{/link}{/if}" title="{lang}wcf.global.button.rss{/lang}" class="jsTooltip"><span class="icon icon16 icon-rss"></span> <span class="invisible">{lang}wcf.global.button.rss{/lang}</span></a></li>
		{if $__wcf->user->userID}
			<li><a title="{lang}wcf.user.objectWatch.manageSubscription{/lang}" class="jsSubscribeButton jsTooltip" data-object-type="de.voolia.news.category" data-object-id="{@$category->categoryID}"><span class="icon icon16 icon-bookmark"></span> <span class="invisible">{lang}wcf.user.objectWatch.manageSubscription{/lang}</span></a></li>
		{/if}
	{else}
		<li><a rel="alternate" href="{if $__wcf->getUser()->userID}{link application='news' controller='NewsFeed' appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}{else}{link application='news' controller='NewsFeed' appendSession=false}{/link}{/if}" title="{lang}wcf.global.button.rss{/lang}" class="jsTooltip"><span class="icon icon16 icon-rss"></span> <span class="invisible">{lang}wcf.global.button.rss{/lang}</span></a></li>
	{/if}
	<li class="jsOnly"><a title="{lang}news.entry.markAllAsRead{/lang}" class="markAllAsReadButton jsTooltip"><span class="icon icon16 icon-ok"></span> <span class="invisible">{lang}news.category.markAllAsRead{/lang}</span></a></li>
{/capture}

{include file='header' sidebarOrientation='right'}

{if $__wcf->getPageMenu()->getLandingPage()->menuItem == 'news.header.menu.news' && !$categoryID}
	<header class="boxHeadline">
		<h1>{PAGE_TITLE|language}</h1>
		{hascontent}<p>{content}{PAGE_DESCRIPTION|language}{/content}</p>{/hascontent}
	</header>
{else}
	<header class="boxHeadline">
		<h1>{lang}news.index.overview.description{/lang}</h1>
	</header>
{/if}

{include file='userNotice'}

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='news' object=$category controller='NewsOverview' link="pageNo=%d"}

	{hascontent}
		<nav>
			<ul>
				{content}
					{if $category}
						{if $category->canUseCategory()}
							<li><a href="{link application='news' controller='NewsAdd'}categoryIDs[]={@$categoryID}{/link}" title="{lang}news.entry.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}news.entry.add{/lang}</span></a></li>
						{/if}
					{else}
						{if $__wcf->getSession()->getPermission('user.news.canAddNews')}<li><a href="{link application='news' controller='NewsAdd'}{/link}" title="{lang}news.entry.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}news.entry.add{/lang}</span></a></li>{/if}
					{/if}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{include file='newsMessageList' application='news'}

<div class="contentNavigation">
	{@$pagesLinks}

	{hascontent}
		<nav>
			<ul>
				{content}
					{if $categoryID}<li><a href="{link application='news' controller='NewsOverview'}{/link}" title="{lang}news.showAll{/lang}" class="button"><span class="icon icon16 icon-reorder"></span> <span>{lang}news.index.showAll{/lang}</span></a></li>{/if}
					{if $category}
						{if $category->canUseCategory()}
							<li><a href="{link application='news' controller='NewsAdd'}categoryIDs[]={@$categoryID}{/link}" title="{lang}news.entry.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}news.entry.add{/lang}</span></a></li>
						{/if}
					{else}
						{if $__wcf->getSession()->getPermission('user.news.canAddNews')}<li><a href="{link application='news' controller='NewsAdd'}{/link}" title="{lang}news.entry.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}news.entry.add{/lang}</span></a></li>{/if}
					{/if}
					{event name='contentNavigationButtonsBottom'}
				{/content}
			</ul>
		</nav>
	{/hascontent}

	{if $__wcf->session->getPermission('mod.news.canEditNews')}
		<nav class="jsClipboardEditor" data-types="[ 'de.voolia.news.entry' ]"></nav>
	{/if}
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
