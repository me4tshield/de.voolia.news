{if $user->newsEntries}
	<dt><a href="{link application='news' controller='NewsOverview'}userID={@$user->userID}{/link}" title="{lang}news.user.profile.newsItems{/lang}" class="jsTooltip">{lang}news.entry.news.title{/lang}</a></dt>
	<dd>{#$user->newsEntries}</dd>
{/if}