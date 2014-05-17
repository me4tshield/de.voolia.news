{include file='documentHeader'}

<head>
	<title>{lang}news.entry.update.{$action}.title{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}

	<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link application='news' controller='NewsFeed'}{/link}" />
</head>

<body id="tpl{$templateName|ucfirst}">

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
	<h1>{lang}news.entry.update.{$action}.title{/lang}</h1>
</header>

{include file='userNotice'}

{include file='formError'}

<form id="messageContainer" class="jsFormGuard" method="post" action="{if $action == 'add'}{link application='news' controller='NewsUpdateAdd' id=$newsID}{/link}{else}{link application='news' controller='NewsUpdateEdit' id=$updateID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}news.entry.add.form.information.title{/lang}</legend>

			{include file='messageFormMultilingualism'}

			<dl{if $errorField == 'subject'} class="formError"{/if}>
				<dt><label for="subject">{lang}news.entry.update.add.form.information.subject.title{/lang}</label></dt>
				<dd>
					<input type="text" id="subject" name="subject" value="{$subject}" maxlength="255" class="long" />
					{if $errorField == 'subject'}
						<small class="innerError">
							{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{/if}
						</small>
					{/if}
				</dd>

				{if $__wcf->getSession()->getPermission('user.news.canSetNewsAsNew')}
					<dt></dt>
					<dd>
						<label><input type="checkbox" name="setNewsAsNew" value="1" /> {lang}news.entry.add.form.newsUpdate.setNewsAsNew.title{/lang}</label>
						<small>{lang}news.entry.add.form.newsUpdate.setNewsAsNew.description{/lang}</small>
					</dd>
				{/if}
			</dl>

			{event name='informationFields'}
		</fieldset>

		<fieldset>
			<legend>{lang}news.entry.update.add.form.message.title{/lang}</legend>

			<dl class="wide">
				<dt><label for="text">{lang}news.entry.update.add.form.message.title{/lang}</label></dt>
				<dd>
					<textarea id="text" name="text" rows="20" cols="40">{$text}</textarea>
				</dd>
			</dl>

			{event name='messageFields'}
		</fieldset>

		{event name='fieldsets'}

		{include file='messageFormTabs' wysiwygContainerID='text'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{include file='messageFormPreviewButton'}
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
{include file='wysiwyg'}

</body>
</html>
