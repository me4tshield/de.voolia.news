<div class="container containerPadding marginTop">
	{if NEWS_ENABLE_NEWSPICTURE}
		<div class="box32">
			<a href="{link application='news' controller='News' object=$_news}{/link}" class="framed">
				<img src="{@$_news->getNewsPicture()->getURL()}" class="newsImage" alt="" />
			</a>
	{/if}

	<div>
		<div class="containerHeadline">
			<h3><a href="{link application='news' controller='News' object=$_news}{/link}" class="newsPreview" data-news-id="{@$_news->newsID}" title="{$_news->subject}">{$_news->subject}</a></h3>
			<p><small><span class="username">{if $_news->userID}<a href="{link controller='User' object=$_news->getUserProfile()}{/link}" class="userLink" data-user-id="{$_news->userID}">{$_news->username}</a>{else}{$_news->username}{/if}</span> - {@$_news->time|time} - {lang}news.entry.bbcode.comments{/lang}</small></p>
		</div>
	</div>

	{if NEWS_ENABLE_NEWSPICTURE}</div>{/if}
</div>