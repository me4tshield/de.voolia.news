{include file='documentHeader'}

<head>
	<title>{lang}news.watchedCategories.title{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}
</head>

<body id="tpl{$templateName|ucfirst}">

{include file='header'}

<header class="boxHeadline">
	<h1>{lang}news.watchedCategories.title{/lang}</h1>
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='news' controller='WatchedCategoryList' link="pageNo=%d"}
</div>

{if $items}
	{include file='newsMessageList' application='news'}
{else}
	<p class="info">{lang}news.watchedCategories.noEntries{/lang}</p>
{/if}

<div class="contentNavigation">
	{@$pagesLinks}
</div>

{include file='footer'}

</body>
</html>
