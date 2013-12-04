{if $__news->isActiveApplication() && $__searchAreaInitialized|empty}
	{capture assign='__searchHiddenInputFields'}<input type="hidden" name="types[]" value="de.voolia.news.entry" />{/capture}
	{capture assign='__searchInputPlaceholder'}{lang}wcf.search.voolia.news.search{/lang}{/capture}
{/if}