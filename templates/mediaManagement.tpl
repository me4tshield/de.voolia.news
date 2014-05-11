{include file='documentHeader'}

<head>
	<title>{lang}news.mediaManagement.title{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}

	<script data-relocate="true" type="text/javascript">
		//<![CDATA[
		$(function() {
			$('#pictureAddManagement').hide();

			$('#pictureAddButton').click(function() {
				$('#pictureAddManagement').wcfDialog({
					title: WCF.Language.get('{lang}news.mediaManagement.browser.pictureUpload.title{/lang}')
				});
			});
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}
	<fieldset>
		<legend>{lang}lorem{/lang}</legend>

		<dl class="plain inlineDataList">
			<button class="button small" id="folderAddButton">{lang}news.mediaManagement.browser.folder.button{/lang}</button>
		</dl>
	</fieldset>
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
				<th class="columnIcon">Aktion</th>
				<th class="columnTitle">Titel</th>
			</thead>
			<tbody>
				<tr>
					<td class="columnIcon" style="text-align: center;"><span class="icon icon-double-angle-left icon16"></span></td>
					<td class="columnTitle">...</td>
				</tr>
				<tr>
					<td class="columnIcon"><span class="icon icon-pencil icon16"></span> <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="Löschen"></span></td>
					<td class="columnTitle"><span class="icon icon-folder-close icon16"></span> <a href="#">Ordner 1</a></td>
				</tr>
				<tr>
					<td class="columnIcon"><span class="icon icon-pencil icon16"></span> <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="Löschen"></span></td>
					<td class="columnTitle"><span class="icon icon-folder-close icon16"></span> <a href="#">Ordner 2</a></td>
				</tr>
				<tr>
					<td class="columnIcon"><span class="icon icon-pencil icon16"></span> <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="Löschen"></span></td>
					<td class="columnTitle"><span class="icon icon-picture icon16"></span> <a href="#">news-picture.jpg</a></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="contentNavigation">
		<nav class="marginTop">
			<ul>
				<li><button class="button small" id="pictureAddButton">{lang}news.mediaManagement.browser.pictureUpload.button{/lang}</button></li>
			</ul>
		</nav>
	</div>
</div>

<div id="pictureAddManagement">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}news.mediaManagement.browser.pictureUpload.picture.title{/lang}</legend>
			<dl>
				<dt><label for="folder">{lang}news.mediaManagement.browser.pictureUpload.picture{/lang}</label></dt>
				<dd>
					<input type="file" name="folder" id="folder" value="" required="required"/>
					<span>{lang}news.mediaManagement.browser.pictureUpload.picture.description{/lang}</span>
				</dd>
			</dl>
		</fieldset>
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
	</div>
</div>

{include file='footer'}
</body>
</html>
