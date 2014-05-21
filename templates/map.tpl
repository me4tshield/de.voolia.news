{include file='documentHeader'}

<head>
	<title>{lang}news.header.menu.news.map{/lang} - {lang}news.header.menu.news{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}

	{include file='googleMapsJavaScript'}
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new News.Map.LargeMap('mapContainer', { }, 'news\\data\\news\\NewsAction', '#geocode');
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}
	{event name='boxes'}

	{@$__boxSidebar}
{/capture}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
	<h1>{lang}news.header.menu.news.map{/lang}</h1>
</header>

{include file='userNotice'}

<div class="container containerPadding marginTop">
	<div id="mapContainer" class="googleMap"></div>
</div>

{include file='footer'}

</body>
</html>
