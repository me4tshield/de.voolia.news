{include file='header' pageTitle='news.acp.menu.link.news.newsBulkProcessing'}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();
		$('#actionSetup input[type=radio]').change(function(event) {
			var $target = $(event.currentTarget);
			if ($target.val() == 'changeLanguage') $('#changeLanguageSetup').show();
			else $('#changeLanguageSetup').hide();
			if ($target.val() == 'move') $('#moveSetup').show();
			else $('#moveSetup').hide();
		});
		{if $action != 'changeLanguage'}$('#changeLanguageSetup').hide();{/if}
		{if $action != 'move'}$('#moveSetup').hide();{/if}
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}news.acp.menu.link.news.newsBulkProcessing{/lang}</h1>
</header>

{include file='formError'}

{if $affectedNews|isset}
	<p class="success">{lang}news.acp.bulkProcessing.success{/lang}</p>	
{/if}

<p class="warning">{lang}news.acp.bulkProcessing.warning{/lang}</p>

{hascontent}
	<div class="contentNavigation">
		<nav>
			<ul>
				{content}
					{event name='contentNavigationButtons'}
				{/content}
			</ul>
		</nav>
	</div>
{/hascontent}

<form method="post" action="{link application='news' controller='NewsBulkProcessing'}{/link}">
	<div class="tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('conditions')}">{lang}news.acp.bulkProcessing.conditions{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('action')}">{lang}news.acp.bulkProcessing.action{/lang}</a></li>
				{event name='tabMenuTabs'}
			</ul>
		</nav>
		
		<div id="conditions" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}news.acp.bulkProcessing.conditions{/lang}</legend>
				
				<dl>
					<dt><label for="time">{lang}news.acp.bulkProcessing.conditions.time{/lang}</label></dt>
					<dd>
						<input type="date" id="timeFrom" name="timeFrom" value="{$timeFrom}" placeholder="{lang}news.acp.bulkProcessing.conditions.time.from{/lang}" />
						<input type="date" id="timeTo" name="timeTo" value="{$timeTo}" placeholder="{lang}news.acp.bulkProcessing.conditions.time.to{/lang}" />
					</dd>
				</dl>
				
				<dl>
					<dt><label for="author">{lang}news.acp.bulkProcessing.conditions.author{/lang}</label></dt>
					<dd>
						<input type="text" id="authors" name="authors" value="{$authors}" class="long" />
					</dd>
				</dl>
				
				<dl>
					<dt><label for="categoryIDs">{lang}news.acp.bulkProcessing.conditions.category{/lang}</label></dt>
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
						<small>{lang}wcf.global.multiSelect{/lang}</small>
					</dd>
				</dl>
				
				{if $languages|count > 1}
					<dl>
						<dt><label for="languageIDs">{lang}news.acp.bulkProcessing.conditions.languageIDs{/lang}</label></dt>
						<dd>
							<select name="languageIDs[]" id="languageIDs" multiple="multiple" size="5">
								<option value="0">{lang}news.acp.bulkProcessing.conditions.languageIDs.noLanguage{/lang}</option>
								{foreach from=$languages item=language}
									<option value="{@$language->languageID}"{if $language->languageID|in_array:$languageIDs} selected="selected"{/if}>{$language->languageName}</option>
								{/foreach}
							</select>
							<small>{lang}wcf.global.multiSelect{/lang}</small>
						</dd>
					</dl>
				{/if}
				
				{event name='conditionFields'}
			</fieldset>
			
			<fieldset>
				<legend>{lang}news.acp.bulkProcessing.conditions.states{/lang}</legend>
				<dl>
					<dt></dt>
					<dd>					
						<label><input type="checkbox" name="isPoll" value="1" {if $isPoll == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isPoll{/lang}</label>
						<label><input type="checkbox" name="isNotPoll" value="1" {if $isNotPoll == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotPoll{/lang}</label>
						<label><input type="checkbox" name="isActive" value="1" {if $isActive == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isActive{/lang}</label>
						<label><input type="checkbox" name="isNotActive" value="1" {if $isNotActive == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotActive{/lang}</label>
						<label><input type="checkbox" name="isDeleted" value="1" {if $isDeleted == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isDeleted{/lang}</label>
						<label><input type="checkbox" name="isNotDeleted" value="1" {if $isNotDeleted == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotDeleted{/lang}</label>
						<label><input type="checkbox" name="isPublished" value="1" {if $isPublished == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isPublished{/lang}</label>
						<label><input type="checkbox" name="isNotPublished" value="1" {if $isNotPublished == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotPublished{/lang}</label>
						<label><input type="checkbox" name="isArchived" value="1" {if $isArchived == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isArchived{/lang}</label>
						<label><input type="checkbox" name="isNotArchived" value="1" {if $isNotArchived == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotArchived{/lang}</label>
						<label><input type="checkbox" name="isHot" value="1" {if $isHot == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isHot{/lang}</label>
						<label><input type="checkbox" name="isNotHot" value="1" {if $isNotHot == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotHot{/lang}</label>
						<label><input type="checkbox" name="isAnnouncement" value="1" {if $isAnnouncement == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isAnnouncement{/lang}</label>
						<label><input type="checkbox" name="isNotAnnouncement" value="1" {if $isNotAnnouncement == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotAnnouncement{/lang}</label>
						<label><input type="checkbox" name="isCommentable" value="1" {if $isCommentable == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isCommentable{/lang}</label>
						<label><input type="checkbox" name="isNotCommentable" value="1" {if $isNotCommentable == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.isNotCommentable{/lang}</label>
						<label><input type="checkbox" name="hasComments" value="1" {if $hasComments == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.hasComments{/lang}</label>
						<label><input type="checkbox" name="hasNoComments" value="1" {if $hasNoComments == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.hasNoComments{/lang}</label>
						<label><input type="checkbox" name="hasCategory" value="1" {if $hasCategory == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.hasCategory{/lang}</label>
						<label><input type="checkbox" name="hasNoCategory" value="1" {if $hasNoCategory == 1}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.conditions.state.hasNoCategory{/lang}</label>
					</dd>
				</dl>
				
				{event name='stateFields'}
			</fieldset>
		</div>
		
		<div id="action" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}news.acp.bulkProcessing.action{/lang}</legend>
				
				<dl id="actionSetup"{if $errorField == 'action'} class="formError"{/if}>
					<dt></dt>
					<dd>
						<label><input type="radio" name="action" value="setAsHot" {if $action == 'setAsHot'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.setAsHot{/lang}</label>
						<label><input type="radio" name="action" value="unsetAsHot" {if $action == 'unsetAsHot'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.unsetAsHot{/lang}</label>
						<label><input type="radio" name="action" value="activateComments" {if $action == 'activateComments'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.activateComments{/lang}</label>
						<label><input type="radio" name="action" value="deactivateComments" {if $action == 'deactivateComments'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.deactivateComments{/lang}</label>
						<label><input type="radio" name="action" value="archive" {if $action == 'archive'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.archive{/lang}</label>
						<label><input type="radio" name="action" value="trash" {if $action == 'trash'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.trash{/lang}</label>
						<label><input type="radio" name="action" value="delete" {if $action == 'delete'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.delete{/lang}</label>
						<label><input type="radio" name="action" value="restore" {if $action == 'restore'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.restore{/lang}</label>
						<label><input type="radio" name="action" value="disable" {if $action == 'disable'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.disable{/lang}</label>
						<label><input type="radio" name="action" value="enable" {if $action == 'enable'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.enable{/lang}</label>
						<label><input type="radio" name="action" value="publish" {if $action == 'publish'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.publish{/lang}</label>
						<label><input type="radio" name="action" value="changeLanguage" {if $action == 'changeLanguage'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.changeLanguage{/lang}</label>
						<label><input type="radio" name="action" value="move" {if $action == 'move'}checked="checked" {/if}/> {lang}news.acp.bulkProcessing.action.move{/lang}</label>
						
						{event name='actions'}
						
						{if $errorField == 'action'}
							<small class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{/if}
							</small>
						{/if}
					</dd>
				</dl>
				
				{event name='actionFields'}
			</fieldset>
			
			<fieldset id="changeLanguageSetup">
				<legend>{lang}news.acp.bulkProcessing.action.changeLanguage{/lang}</legend>
				
				<dl>
					<dt><label for="newLanguageID">{lang}news.acp.bulkProcessing.action.changeLanguage.newLanguageID{/lang}</label></dt>
					<dd>
						<select name="newLanguageID" id="newLanguageID">
							{foreach from=$languages item=language}
								<option value="{@$language->languageID}"{if $language->languageID == $newLanguageID} selected="selected"{/if}>{$language->languageName}</option>
							{foreachelse}
								<option value="0">{lang}news.acp.bulkProcessing.action.changeLanguage.noLanguage{/lang}</option>
							{/foreach}
						</select>
					</dd>
				</dl>
			</fieldset>
			
			<fieldset id="moveSetup">
				<legend>{lang}news.acp.bulkProcessing.action.move{/lang}</legend>
				
				<dl>
					<dt><label for="move">{lang}news.acp.bulkProcessing.action.move.to{/lang}</label></dt>
					<dd>
						<select name="moveCategoryID" id="moveCategoryID">
							<option value="0">{lang}news.acp.bulkProcessing.action.move.noCat{/lang}</option>
							
							{foreach from=$categoryList item=categoryItem}
								{if $categoryItem->canUseCategory()}
									<option value="{@$categoryItem->categoryID}"{if $categoryItem->categoryID} selected="selected"{/if} data-can-add-sources="{$categoryItem->getPermission('canAddSources')}" data-can-set-tags="{$categoryItem->getPermission('canSetTags')}">{$categoryItem->getTitle()}</option>
									{if $categoryItem->hasChildren()}
										{foreach from=$categoryItem item=subCategoryItem}
											{if $subCategoryItem->canUseCategory()}
												<option value="{@$subCategoryItem->categoryID}"{if $subCategoryItem->categoryID} selected="selected"{/if} data-can-add-sources="{$subCategoryItem->getPermission('canAddSources')}" data-can-set-tags="{$subCategoryItem->getPermission('canSetTags')}">&nbsp; &nbsp; {$subCategoryItem->getTitle()}</option>
											{/if}
										{/foreach}
									{/if}
								{/if}
							{/foreach}
						</select>
					</dd>
				</dl>
			</fieldset>
		</div>
		
		{event name='tabMenuContents'}
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
