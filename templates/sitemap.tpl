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
			{foreach from=$categoryNodeList item=categoryNode}
				<li>
					<a href="{link application='news' controller='NewsOverview' object=$categoryNode->getDecoratedObject()}{/link}">{$categoryNode->getDecoratedObject()->title|language}</a>

				{if $categoryNode->hasChildren()}<ul>{else}<ul></ul></li>{/if}

				{if !$categoryNode->hasChildren() && $categoryNode->isLastSibling()}
					{@"</ul></li>"|str_repeat:$categoryNode->getOpenParentNodes()}
				{/if}
			{/foreach}
		</ul>
	</li>
</ul>
