{if $action == 'add'}{include file='header' pageTitle='news.acp.entry.picture.category.add'}{else}{include file='header' pageTitle='news.acp.entry.picture.category.edit'}{/if}

<header class="boxHeadline">
	<h1>{lang}news.acp.entry.picture.category.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}news.acp.entry.picture.category.{$action}.success{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='NewsPictureCategoryList' application='news'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}news.acp.menu.link.news.picture.category.list{/lang}</span></a></li>

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='NewsPictureCategoryAdd' application='news'}{/link}{else}{link controller='NewsPictureCategoryEdit' application='news' id=$categoryID}{/link}{/if}">
	<div id="general" class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>

			<dl{if $errorField == 'categoryName'} class="formError"{/if}>
				<dt><label for="databaseName">{lang}news.acp.entry.picture.category.categoryName{/lang}</label></dt>
				<dd>
					<input type="text" id="categoryName" name="categoryName" value="{$categoryName}" autofocus="autofocus" class="long" />
					{if $errorField == 'categoryName'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{/if}
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
	</div>
</form>

{include file='footer'}
