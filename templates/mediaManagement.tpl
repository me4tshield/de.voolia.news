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
	<fieldset>
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

	<form enctype="multipart/form-data" method="post" action="{link application='news' controller='MediaManagement'}{/link}">
		<fieldset>
			<legend>{lang}news.mediaManagement.browser.media.upload.title{/lang}</legend>
			<dl>
				<dt></dt>
				<dd>
					<input type="file" name="file" id="file" value="" required="required"/>
					<span>{lang}news.mediaManagement.browser.media.upload.description{/lang}</span>
				</dd>
			</dl>
			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
			</div>
		</fieldset>
	</form>
{/capture}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
	<h1>{lang}news.mediaManagement.title{/lang}</h1>
</header>

{include file='userNotice'}

<div id="pictureManagement">
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}news.mediaManagement.browser.title{/lang}</h2>
		</header>

		<table class="table">
			<thead>
				<th class="columnIcon">{lang}news.mediaManagement.browser.table.action{/lang}</th>
				<th class="columnTitle">{lang}news.mediaManagement.browser.table.name{/lang}</th>
				<th class="columnTitle">{lang}news.mediaManagement.browser.table.typ{/lang}</th>
			</thead>
			<tbody>
				{foreach from=$objects item=media}
					<tr>
						<td class="columnIcon"><span class="icon icon-pencil icon16"></span> <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="Löschen"></span></td>
						<td class="columnTitle"><span class="icon icon-{if $media->typ == 'picture'}picture{else}film{/if} icon16"></span> <a class="jsMediaPreview">{$media->title}.{$media->fileExtension}</a></td>
						<td class="columnTitle">{$media->typ}/{$media->fileExtension}</td>
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

{include file='footer'}
</body>
</html>
