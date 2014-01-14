<fieldset>
	<legend>{lang}news.sidebar.categoryList.title{/lang}</legend>
	<div>
		<ol class="sidebarNestedCategoryList">
			{foreach from=$categoryList item=categoryItem}
				{if $categoryItem->isAccessible()}
				<li{if $category && $category->categoryID == $categoryItem->categoryID} class="active"{/if}>
					<a href="{link application='news' controller='NewsOverview' object=$categoryItem->getDecoratedObject()}{/link}">{$categoryItem->getTitle()}</a>
					{if $categoryItem->getUnreadNews()}<span class="badge badgeUpdate"><a href="{link application='news' controller='UnreadNewsList'}{/link}" title="{lang}news.sidebar.categoryList.newsUpdate{/lang}" class="jsTooltip">{#$categoryItem->getNews()}</a></span>{else}<span class="badge">{#$categoryItem->getNews()}</span>{/if}
					{if $categoryItem->hasChildren()}
						<ol>
							{foreach from=$categoryItem item=subCategoryItem}
								{if $subCategoryItem->isAccessible()}
								<li{if $category && $category->categoryID == $subCategoryItem->categoryID} class="active"{/if}>
									<a href="{link application='news' controller='NewsOverview' object=$subCategoryItem->getDecoratedObject()}{/link}">{$subCategoryItem->getTitle()}</a>
									<span class="badge">{#$subCategoryItem->getNews()}</span>
								</li>
								{/if}
							{/foreach}
						</ol>
					{/if}
				</li>
				{/if}
			{/foreach}
		</ol>
	</div>
</fieldset>
