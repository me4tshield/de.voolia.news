{include file='documentHeader'}

<head>
	<title>{lang}news.entry.{$action}.title{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}

	<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link application='news' controller='NewsFeed'}{/link}" />

	<script data-relocate="true" src="{@$__wcf->getPath('news')}js/News.Picture{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true" type="text/javascript">
		//<![CDATA[
		$(function() {
			WCF.Language.addObject({
				'news.entry.add.form.button.sources.addSource': '{lang}news.entry.add.form.button.sources.addSource{/lang}',
				'news.entry.add.form.button.sources.removeSource': '{lang}news.entry.add.form.button.sources.removeSource{/lang}',
				'news.entry.add.form.information.sources.input.link': '{lang}news.entry.add.form.information.sources.input.link{/lang}',
				'news.entry.add.form.information.sources.input.title': '{lang}news.entry.add.form.information.sources.input.title{/lang}',
				'news.entry.picture.error.tooLarge': '{lang}news.entry.picture.error.tooLarge{/lang}',
				'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
			});

			$('#enableDelayedPublication').click(function() {
				$('#publicationDate').parents('dl:eq(0)').toggle();
			});
			$('#enableAutomaticArchiving').click(function() {
				$('#archivingDate').parents('dl:eq(0)').toggle();
			});

			{if NEWS_ENABLE_NEWSPICTURE}
				WCF.Language.addObject({
					'news.entry.picture.add': '{lang}news.entry.picture.add{/lang}'
				});
				new News.Picture.Managment($('#pictureInspectorButton'), $('#pictureID'));
			{/if}

			{include file='__messageQuoteManager' wysiwygSelector='text'}
			new News.QuoteHandler($quoteManager);

			{if NEWS_ENTRY_ENABLE_SOURCES}
				new News.Source.Managment('sourceContainer', [ {implode from=$sources item=source}{ sourceText: '{$source[sourceText]|encodeJS}', sourceLink: '{$source[sourceLink]|encodeJS}' }{/implode} ], {@NEWS_ENTRY_SOURCES_MAXCOUNT});
				var $sourceContainer = $('#sourceContainer').parent();
			{/if}

			var $tagsContainer = $('#tagSearchInput').parents('dl');

			$('#categoryIDs').change(function() {
				{if NEWS_ENTRY_ENABLE_SOURCES}
					var canAddSources = true;
					$('#categoryIDs option').filter(':selected').each(function(index, option) {
						if ($(option).data('canAddSources') == false) {
							canAddSources = false;
							return false;
						}
					});

					if (canAddSources) $sourceContainer.show();
					else $sourceContainer.hide();
				{/if}

				var canSetTags = true;
				$('#categoryIDs option').filter(':selected').each(function(index, option) {
					if ($(option).data('canSetTags') == false) {
						canSetTags = false;
						return false;
					}
				});

				if (canSetTags) $tagsContainer.show();
				else $tagsContainer.hide();
			});
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{include file='header'}

<header class="boxHeadline">
	<h1>{lang}news.entry.{$action}.title{/lang}</h1>
	<p>{if $action == 'edit' && $news->editTime}{lang}news.entry.edit.lastEditTime{/lang}{/if}</p>
</header>

{include file='userNotice'}

{include file='formError'}

<form id="messageContainer" class="jsFormGuard" method="post" action="{if $action == 'add'}{link application='news' controller='NewsAdd'}{/link}{else}{link application='news' controller='NewsEdit' id=$newsID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}news.entry.add.form.information.title{/lang}</legend>

			{include file='messageFormMultilingualism'}

			<dl{if $errorField == 'subject'} class="formError"{/if}>
				<dt><label for="subject">{lang}news.entry.add.form.information.subject.title{/lang}</label></dt>
				<dd>
					<input type="text" id="subject" name="subject" value="{$subject}" maxlength="255" class="long" />
					{if $errorField == 'subject'}
						<small class="innerError">
							{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{/if}
						</small>
					{/if}
				</dd>
			</dl>

			<dl{if $errorField == 'teaser'} class="formError"{/if}>
				<dt><label for="teaser">{lang}news.entry.add.form.information.teaser.title{/lang}</label></dt>
				<dd>
					<textarea id="teaser" name="teaser" rows="5" cols="40">{$teaser}</textarea>
					{if $errorField == 'teaser'}
						<small class="innerError">
							{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{/if}
						</small>
					{/if}
					<small>{lang}news.entry.add.form.information.teaser.description{/lang}</small>
				</dd>
			</dl>

			{if MODULE_TAGGING && NEWS_ENABLE_TAGS}{include file='tagInput'}{/if}

			{if NEWS_ENTRY_ENABLE_SOURCES}
				<dl{if $errorField == 'sources'} class="formError"{/if}>
					<dt><label>{lang}news.entry.add.form.information.sources.title{/lang}</label></dt>
					<dd class="sortableListContainer" id="sourceContainer">
						<ol{if NEWS_ENTRY_SOURCES_MAXCOUNT!= 1} class="sortableList"{/if}></ol>
						{if $errorField == 'sources'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{/if}
							</small>
						{/if}
						<small>{lang}news.entry.add.form.information.sources.description{/lang}</small>
					</dd>
				</dl>
			{/if}

			{if $action == 'edit'}
				<dl{if $errorField == 'editReason'} class="formError"{/if}>
					<dt><label for="editReason">{lang}news.entry.add.form.settings.editReason.title{/lang}</label></dt>
					<dd>
						<input type="text" id="editReason" name="editReason" value="{$editReason}" maxlength="255" class="long" />
						{if $errorField == 'editReason'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>

				{if $__wcf->getSession()->getPermission('mod.news.canEditNewsWithoutNote')}
					<dl>
						<dd>
							<label><input type="checkbox" name="editNoteSuppress" value="1"{if $editNoteSuppress} checked="checked"{/if} /> {lang}news.entry.add.form.settings.editReason.optional{/lang}</label>
							{if $errorField == 'editNoteSuppress'}
								<small class="innerError">
									{if $errorType == 'empty'}
										{lang}wcf.global.form.error.empty{/lang}
									{/if}
								</small>
							{/if}
						</dd>
					</dl>
				{/if}
			{/if}

			{event name='informationFields'}
		</fieldset>

		<fieldset>
			<legend>{lang}news.entry.add.form.settings.title{/lang}</legend>

			{if NEWS_ENABLE_NEWSPICTURE}
				<dl class="pictureInput{if $errorField == 'newsPicture'} formError{/if}">
					<dt><label for="newsPicture">{lang}news.entry.add.form.settings.newspicture.title{/lang}</label></dt>
					<dd>
						<ul>
							{if $picture}
								<li class="box32">
									<div class="framed">
										<img src="{$picture->getURL()}" alt="" class="newsImage" />
									</div>
									<div>
										<div>
											<p>{$picture->title}</p>
											<small>{@$picture->filesize|filesize}</small>
										</div>
									</div>
								</li>
							{else}
								<li class="box32">
									<div class="framed">
										<img src="{@$__wcf->getPath('news')}images/newspictureDummy.png" alt="" class="newsImage" />
									</div>
								</li>
							{/if}
						</ul>

						<div id="pictureInspectorButton">
							<span class="button small">{lang}news.entry.add.form.settings.newspicture.search{/lang}</span>
						</div>
						{if $errorField == 'newsPicture'}
							<small class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{/if}
							</small>
						{/if}
					</dd>
				</dl>
			{/if}

			<dl>
				<dt><label for="categoryIDs">{lang}news.entry.add.form.settings.category.title{/lang}</label></dt>
				<dd>
					<select id="categoryIDs" name="categoryIDs[]" multiple="multiple" size="8" class="medium">
						{foreach from=$categoryList item=categoryItem}
							{if $categoryItem->canUseCategory()}
								<option value="{@$categoryItem->categoryID}"{if $categoryItem->categoryID|in_array:$categoryIDs} selected="selected"{/if} data-can-add-sources="{$categoryItem->getPermission('canAddSources')}" data-can-set-tags="{$categoryItem->getPermission('canSetTags')}">{$categoryItem->getTitle()}</option>
								{if $categoryItem->hasChildren()}
									{foreach from=$categoryItem item=subCategoryItem}
										{if $subCategoryItem->canUseCategory()}
											<option value="{@$subCategoryItem->categoryID}"{if $subCategoryItem->categoryID|in_array:$categoryIDs} selected="selected"{/if} data-can-add-sources="{$subCategoryItem->getPermission('canAddSources')}" data-can-set-tags="{$subCategoryItem->getPermission('canSetTags')}">&nbsp; &nbsp; {$subCategoryItem->getTitle()}</option>
										{/if}
									{/foreach}
								{/if}
							{/if}
						{/foreach}
					</select>
					{if $errorField == 'categoryIDs'}
						<small class="innerError">
							{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{/if}
						</small>
					{/if}
					<small>{lang}news.entry.add.form.settings.category.description{/lang}</small>
				</dd>
			</dl>

			<dl>
				<dt></dt>
				<dd>
					<label><input type="checkbox" name="isHot" value="1"{if $isHot} checked="checked"{/if} /> {lang}news.entry.add.form.settings.isHot.title{/lang}</label>
				</dd>
			</dl>

			{event name='settingsFields'}
		</fieldset>

		<fieldset class="jsOnly">
			<legend>{lang}news.entry.publication{/lang}</legend>

			<dl>
				<dt></dt>
				<dd>
					<label><input type="checkbox" id="enableDelayedPublication" name="enableDelayedPublication" value="1"{if $enableDelayedPublication} checked="checked"{/if} /> {lang}news.entry.publication.enableDelayedPublication{/lang}</label>
				</dd>
			</dl>

			<dl{if $errorField == 'publicationDate'} class="formError"{/if}{if !$enableDelayedPublication} style="display: none"{/if}>
				<dt><label for="publicationDate">{lang}news.entry.publicationDate{/lang}</label></dt>
				<dd>
					<input type="datetime" id="publicationDate" name="publicationDate" value="{$publicationDate}" />
					{if $errorField == 'publicationDate'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}news.entry.publicationDate.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>

			<dl>
				<dt></dt>
				<dd>
					<label><input type="checkbox" id="enableAutomaticArchiving" name="enableAutomaticArchiving" value="1"{if $enableAutomaticArchiving} checked="checked"{/if} /> {lang}news.entry.enableAutomaticArchiving{/lang}</label>
				</dd>
			</dl>

			<dl{if $errorField == 'archivingDate'} class="formError"{/if}{if !$enableAutomaticArchiving} style="display: none"{/if}>
				<dt><label for="archivingDate">{lang}news.entry.archivingDate{/lang}</label></dt>
				<dd>
					<input type="datetime" id="archivingDate" name="archivingDate" value="{$archivingDate}" />
					{if $errorField == 'archivingDate'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}news.entry.archivingDate.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>

			{event name='publicationFields'}
		</fieldset>

		<fieldset>
			<legend>{lang}news.entry.add.form.message.title{/lang}</legend>

			<dl class="wide">
				<dt><label for="text">{lang}news.entry.add.form.message.title{/lang}</label></dt>
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
		<input type="hidden" id="pictureID" name="pictureID" value="{@$pictureID}" />
		{include file='messageFormPreviewButton'}
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
{include file='wysiwyg'}

</body>
</html>