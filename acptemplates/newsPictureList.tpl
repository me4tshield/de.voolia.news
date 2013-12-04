{include file='header' pageTitle='news.acp.menu.link.news.picture.list'}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		{if $__wcf->session->getPermission('admin.news.canManageNewsPicture')}
			new WCF.Action.Delete('news\\data\\news\\picture\\NewsPictureAction', '.jsCategoryGroupRow');
		{/if}
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}news.acp.entry.picture.list{/lang}</h1>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='news' controller='NewsPictureAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}news.acp.entry.picture.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $categoryList}
	<div class="tabMenuContainer container containerPadding marginTop">
		<nav class="menu">
			<ul>
				{foreach from=$categoryList item=categoryItem}
					<li{if $categoryID == $categoryItem->categoryID} class="ui-state-active"{/if}><a href="{link controller='NewsPictureList' application='news' id=$categoryItem->categoryID}{/link}">{$categoryItem->getTitle()}</a></li>
					{if $categoryID == $categoryItem->categoryID}
						{assign var=activeCategoryItem value=$categoryItem}
					{/if}
				{/foreach}
			</ul>
		</nav>
		<section id="newspictureList" class="sortableListContainer">
			{if $pictures[$categoryID]|isset}
				<fieldset class="jsCategoryGroup" data-object-id="{@$categoryID}">
					<ol>
						{foreach from=$pictures[$categoryID] item=picture}
							<li class="jsCategoryGroupRow" data-object-id="{@$picture->pictureID}" style="margin: 5px; width: 70px; float: left; text-align: center;">
								<span>
									<a href="{link controller='NewsPictureEdit' application='news' id=$picture->pictureID}{/link}">
										<img src="{@$picture->getURL()}" alt="" style="width: 35px; height: 35px;" />
										<p>{$picture->title|language}</p>
									</a>
									<span class="statusDisplay sortableButtonContainer">
										<a href="{link controller='NewsPictureEdit' application='news' id=$picture->pictureID}{/link}"><span title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip icon icon16 icon-pencil" /></a>
										<span title="{lang}wcf.global.button.delete{/lang}" class="jsDeleteButton jsTooltip icon icon16 icon-remove" data-object-id="{@$picture->pictureID}" data-confirm-message="{lang}news.acp.entry.picture.button.delete.sure{/lang}" />

										{event name='itemButtons'}
									</span>
								</span>
							</li>
						{/foreach}
					</ol>
				</fieldset>
			{/if}

			{foreach from=$activeCategoryItem item=childCategory}
				<fieldset class="jsCategoryGroup" data-object-id="{@$childCategory->categoryID}">
					<legend>{$childCategory->getTitle()}</legend>
					{if $pictures[$childCategory->categoryID]|isset}
						<ol>
							{foreach from=$pictures[$childCategory->categoryID] item=picture}
								<li class="jsCategoryGroupRow" data-object-id="{@$picture->pictureID}" style="margin: 5px; width: 70px; float: left; text-align: center;">
									<span>
										<a href="{link controller='NewsPictureEdit' application='news' id=$picture->pictureID}{/link}">
											<img src="{@$picture->getURL()}" alt="" style="width: 35px; height: 35px;" />
											<p>{$picture->title|language}</p>
										</a>
										<span class="statusDisplay sortableButtonContainer">
											<a href="{link controller='NewsPictureEdit' application='news' id=$picture->pictureID}{/link}"><span title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip icon icon16 icon-pencil" /></a>
											<span title="{lang}wcf.global.button.delete{/lang}" class="jsDeleteButton jsTooltip icon icon16 icon-remove" data-object-id="{@$picture->pictureID}" data-confirm-message="{lang}news.acp.entry.picture.button.delete.sure{/lang}" />

											{event name='itemButtons'}
										</span>
									</span>
								</li>
							{/foreach}
						</ol>
					{else}
						<p class="info">{lang}news.entry.picture.noAvailable{/lang}</p>
					{/if}
				</fieldset>
			{/foreach}
		</section>
	</div>
	<div class="formSubmit">
		<button class="button" data-type="submit">{lang}wcf.global.button.submit{/lang}</button>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
