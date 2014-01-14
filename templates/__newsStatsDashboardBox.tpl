{if $newsStatsDashboardBox|isset}
	<!-- news entries -->
	<dt>{lang}news.entry.title{/lang}</dt>
	<dd>{#$newsStatsDashboardBox[news]}</dd>

	<!-- news comments -->
	<dt>{lang}news.entry.comments.statisticBox{/lang}</dt>
	<dd>{#$newsStatsDashboardBox[comments]}</dd>
{/if}
