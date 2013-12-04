{include file='header' pageTitle='news.acp.entry.picture.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}news.acp.entry.picture.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}news.acp.menu.link.news.picture.{$action}.success{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='NewsPictureList' application='news'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}news.acp.menu.link.news.picture.list{/lang}</span></a></li>

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

{if $categoryList->hasChildren()}
	<form method="post" action="{if $action == 'add'}{link controller='NewsPictureAdd' application='news'}{/link}{else}{link controller='NewsPictureEdit' application='news' id=$pictureID}{/link}{/if}">
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

				<dl{if $errorField == 'picture'} class="formError"{/if} id="picture">
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
						new News.Picture.Upload();
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
{else}
	<p class="error">{lang}news.acp.entry.picture.error.noCategories{/lang}</p>
{/if}

{include file='footer'}
