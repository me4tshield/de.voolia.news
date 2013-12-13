<div class="container containerPadding marginTop">
	{if NEWS_ENABLE_NEWSPICTURE}
		<div class="box32">
			<a href="{link application='news' controller='News' object=$news}{/link}" class="framed">
				<img src="{@$news->getNewsPicture()->getURL()}" class="newsImage" alt="" />
			</a>
	{/if}

	<div>
		<div class="containerHeadline">
			<h3><a href="{link application='news' controller='News' object=$news}{/link}" class="newsPreview" data-news-id="{@$news->newsID}" title="{$news->subject}">{$news->subject}</a></h3>
			<p><small><span class="username"><a href="{link controller='User' object=$news->getUserProfile()}{/link}" class="userLink" data-user-id="{$news->userID}">{$news->username}</a></span> - {@$news->time|time} - {lang}news.entry.bbcode.comments{/lang}</small></p>
		</div>
	</div>

	{if NEWS_ENABLE_NEWSPICTURE}</div>{/if}
</div>