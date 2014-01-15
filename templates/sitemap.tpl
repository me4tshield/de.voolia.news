<ul class="sitemapList">
	<li>
		<h3>{lang}wcf.page.sitemap.news.general{/lang}</h3>
		<ul>
			<li>
				<a href="{link application='news' controller='NewsOverview'}{/link}">{lang}wcf.page.sitemap.news.overview{/lang}</a>
				<a href="{link application='news' controller='NewsArchive'}{/link}">{lang}wcf.page.sitemap.news.archive{/lang}</a>
			</li>
		</ul>
	</li>
	<li>
		<h3>{lang}wcf.page.sitemap.news.categories{/lang}</h3>
		<ul>
			{foreach from=$categoryList item=category}
				<li>
					<a href="{link application='news' controller='NewsOverview' object=$category->getDecoratedObject()}{/link}">{$category->getDecoratedObject()->title|language}</a>

				{if $category->hasChildren()}
					<ul>
				{else}
					</li>

					{if $category->isLastSibling()}
						{@"</ul></li>"|str_repeat:$category->getOpenParentNodes()}
					{/if}
				{/if}
			{/foreach}
		</ul>
	</li>
</ul>
