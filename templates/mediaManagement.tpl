{include file='documentHeader'}

<head>
	<title>{lang}news.mediaManagement.title{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}

	<script data-relocate="true" type="text/javascript">
		//<![CDATA[
		$(function() {
			// MediaPreview
			var $jsMediaPreview = $('<div id="jsMediaPreview"><fieldset><legend></legend>Lorem</fieldset></div>');
			$(".jsMediaPreview").on("click", function() {
				$jsMediaPreview.wcfDialog({ "title": "{lang}news.mediaManagement.browser.media.preview.title{/lang}"});
			});
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}
	<fieldset id="mediaManagementSidebarFilter" class="dashboardBox">
		<legend>{lang}news.sidebar.mediaManagement.type.title{/lang}</legend>

		<ul class="buttonGroup">
			<li>
				<a class="button" href="{link controller='MediaManagement' application='news'}type=picture{/link}">
					<span class="icon icon16 icon-picture"></span>
					<span>news.sidebar.mediaManagement.type.pictures</span>
				</a>
			</li>
			<li>
				<a class="button" href="{link controller='MediaManagement' application='news'}type=video{/link}">
					<span class="icon icon16 icon-film"></span>
					<span>news.sidebar.mediaManagement.type.videos</span>
				</a>
			</li>
		</ul>
	</fieldset>

	<fieldset class="dashboardBox">
		<legend>{lang}news.sidebar.categoryList.title{/lang}</legend>

		<div>
			<ol class="sidebarNestedCategoryList newsSidebarCategoryList">
				{foreach from=$categoryList item=categoryItem}
					<li{if $category && $category->categoryID == $categoryItem->categoryID} class="active"{/if}>
						<a href="{link application='news' controller='MediaManagement' object=$categoryItem->getDecoratedObject()}{/link}">{$categoryItem->getTitle()}</a>
						{if $categoryItem->hasChildren()}
							<ol>
								{foreach from=$categoryItem item=subCategoryItem}
									<li{if $category && $category->categoryID == $subCategoryItem->categoryID} class="active"{/if}>
										<a href="{link application='news' controller='MediaManagement' object=$subCategoryItem->getDecoratedObject()}{/link}">{$subCategoryItem->getTitle()}</a>
									</li>
								{/foreach}
							</ol>
						{/if}
					</li>
				{/foreach}
			</ol>
		</div>
	</fieldset>
{/capture}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
	<h1>{lang}news.mediaManagement.title{/lang}</h1>
</header>

{include file='userNotice'}

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.edit{/lang}</p>
{/if}

<div id="pictureManagement">
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}news.mediaManagement.browser.title{/lang}</h2>
		</header>

		<table class="table">
			<thead>
				<th class="columnIcon">{lang}news.mediaManagement.browser.table.action{/lang}</th>
				<th class="columnTitle">{lang}news.mediaManagement.browser.table.name{/lang}</th>
				<th class="columnTitle">{lang}news.mediaManagement.browser.table.type{/lang}</th>
			</thead>
			<tbody>
				{foreach from=$objects item=media}
					<tr>
						<td class="columnIcon"><span class="icon icon-pencil icon16"></span> <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="Löschen"></span></td>
						<td class="columnTitle"><span class="icon icon-{if $media->type == 'picture'}picture{else}film{/if} icon16"></span> <a class="jsMediaPreview">{$media->title}.{$media->fileExtension}</a></td>
						<td class="columnTitle">{$media->type}/{$media->fileExtension}</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="3">{lang}wcf.global.noItems{/lang}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

	<div class="contentNavigation">
		<nav class="marginTop">
			<ul>
				<li><button class="button small" id="pictureAddButton">{lang}news.mediaManagement.browser.media.upload.button{/lang}</button></li>
			</ul>
		</nav>
	</div>
</div>

<form method="post" action="{link controller='MediaManagement' application='news'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>
			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" value="{$title}" class="long" />
					{if $errorField == 'title'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>

			<dl class="pictureInput{if $errorField == 'picture'} formError{/if}">
				<dt><label>{lang}news.entry.picture{/lang}</label></dt>
				<dd>
					<ul>
						{if $picture}
							<li class="box32 framed">
								<img src="{$picture->getURL()}" alt="" style="width: 32px" />
								<div>
									<div>
										<p>{$picture->title}</p>
										<small>{@$picture->filesize|filesize}</small>
									</div>
								</div>
							</li>
						{/if}
					</ul>

					{* placeholder for upload button: *}
					<div id="picturePlaceholder"></div>
					{if $errorField == 'picture'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}news.acp.entry.picture.error.{$errorType}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}news.entry.picture.limits{/lang}</small>
				</dd>
			</dl>

			<script data-relocate="true">
				//<![CDATA[
				$(function() {
					WCF.Language.addObject({
						'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
					});
					new News.MediaManagement.Upload();
				});
				//]]>
			</script>

			<dl>
				<dt><label for="subject">{lang}news.entry.add.form.settings.category.title{/lang}</label></dt>
				<dd>
					<select id="categoryID" name="categoryID">
						{foreach from=$categoryList item=categoryItem}
							<option value="{@$categoryItem->categoryID}"{if $categoryItem->categoryID == $categoryID} selected="selected"{/if}>{$categoryItem->getTitle()}</option>
							{if $categoryItem->hasChildren()}
								{foreach from=$categoryItem item=subCategoryItem}
									<option value="{@$subCategoryItem->categoryID}"{if $subCategoryItem->categoryID == $categoryID} selected="selected"{/if}>&nbsp; &nbsp; {$subCategoryItem->getTitle()}</option>
								{/foreach}
							{/if}
						{/foreach}
					</select>
					{if $errorField == 'categoryID'}
						<small class="innerError">
							{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{/if}
						</small>
					{/if}
				</dd>
			</dl>

			{event name='dataFields'}
		</fieldset>

		{event name='generalFieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
		<input type="hidden" id="pictureID" name="id" value="{@$pictureID}" />
	</div>
</form>

{include file='footer'}
</body>
</html>
