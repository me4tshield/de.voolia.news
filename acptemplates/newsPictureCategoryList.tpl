{include file='header' pageTitle='news.acp.menu.link.news.picture.category.list'}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		{if $__wcf->session->getPermission('admin.news.canManageNewsPictureCategory')}
			new WCF.Action.Delete('news\\data\\news\\picture\\category\\NewsPictureCategoryAction', '.jsCategoryGroupRow');
		{/if}
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}news.acp.entry.picture.category.list{/lang}</h1>
</header>

{if $success}
	<p class="success">{lang}news.acp.entry.picture.category.add.success{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='news' controller='NewsPictureCategoryAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}news.acp.entry.picture.category.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}news.acp.entry.picture.category.list{/lang}</h2>
		</header>

		<table class="table">
			<thead>
				<tr>
					<th colspan="2" class="columnID columnCategoryID{if $sortField == 'categoryID'} active {@$sortOrder}{/if}"><a href="{link application='news' controller='NewsPictureCategoryList'}sortField=categoryID&sortOrder={if $sortField == 'categoryID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">ID</a></th>
					<th class="columnTitle columnCategoryName{if $sortField == 'categoryName'} active {@$sortOrder}{/if}"><a href="{link application='news' controller='NewsPictureCategoryList'}sortField=categoryName&sortOrder={if $sortField == 'categoryName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}news.acp.entry.picture.category.categoryName{/lang}</a></th>

					{event name='columnHeads'}
				</tr>
			</thead>

			<tbody>
				{foreach from=$objects item=category}
					<tr class="jsCategoryGroupRow">
						<td class="columnIcon">
							<a href="{link controller='NewsPictureCategoryEdit' application='news' object=$category}{/link}" title="{lang}news.acp.entry.picture.category.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}news.acp.entry.picture.category.button.delete{/lang}" data-object-id="{@$category->categoryID}" data-confirm-message="{lang}{lang}news.acp.entry.picture.category.button.delete.sure{/lang}{/lang}"></span>
						</td>
						<td class="columnID">{$category->categoryID}</td>
						<td class="columnTitle columnCategoryName"><a href="{link controller='NewsPictureCategoryEdit' application='news' object=$category}{/link}">{$category->categoryName}</a></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
