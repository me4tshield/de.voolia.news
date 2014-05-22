WCF.Language.addObject({
	'news.mediaManagement.title': '{lang}news.mediaManagement.title{/lang}'
});

if ($config.extraPlugins.length) {
	$config.extraPlugins += ',';
}

// add plugin
$config.extraPlugins += 'vooliaNewsPlugin';

// add button to toolbar
$config.toolbar[$config.toolbar.length - 1].push('__news_openMediaManagementBrowser');
