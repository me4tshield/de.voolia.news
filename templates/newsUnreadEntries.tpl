{include file='documentHeader'}

<head>
	<title>{lang}news.header.menu.news.unreadEntries{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}

	{if $__wcf->getUser()->userID}
		<link rel="alternate" type="application/rss+xml" href="{link application='news' controller='NewsFeed' appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}" title="{lang}wcf.global.button.rss{/lang}" />
	{else}
		<link rel="alternate" type="application/rss+xml" href="{link application='news' controller='NewsFeed' appendSession=false}{/link}" title="{lang}wcf.global.button.rss{/lang}" />
	{/if}
</head>

<body id="tpl{$templateName|ucfirst}">

{include file='header'}

<header class="boxHeadline">
	<h1>{lang}news.header.menu.news.unreadEntries{/lang}</h1>
</header>

{include file='userNotice'}

{include file='newsMessageList' application='news'}

{include file='footer'}

</body>
</html>
