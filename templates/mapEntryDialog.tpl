{if NEWS_ENABLE_NEWSPICTURE}
	<div class="box48">
		<a href="{link application='news' controller='News' object=$news}{/link}" class="framed"><img src="{@$news->getNewsPicture()->getURL()}" alt="" style="width: 48px; height: 48px;" /></a>
{/if}

<div>
	<div class="containerHeadline">
		<h3><a href="{link application='news' controller='News' object=$news}{/link}">{$news->subject}</a> <small>- {@$news->time|time}</small></h3>
	</div>

	<div>
		{if $news->teaser}
			{$news->teaser}
		{else}
			{@$news->getExcerpt()|nl2br}
		{/if}
	</div>

	{event name='previewData'}
</div>

{if NEWS_ENABLE_NEWSPICTURE}</div>{/if}
