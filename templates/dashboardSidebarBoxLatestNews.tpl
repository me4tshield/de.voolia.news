{if $__wcf->getSession()->getPermission('user.news.canViewNews')}
	<ul class="sidebarBoxList">
		{foreach from=$newsList item=news}
			<li{if NEWS_ENABLE_NEWSPICTURE} class="box24"{/if}>
				{if NEWS_ENABLE_NEWSPICTURE}
					<a href="{link application='news' controller='News' object=$news}{/link}" class="framed">
						<img src="{@$news->getNewsPicture()->getURL()}" class="newsImageSidebar" alt="" />
					</a>
				{/if}

				<div class="sidebarBoxHeadline">
					<h3><a href="{link application='news' controller='News' object=$news}{/link}" class="newsPreview" data-news-id="{@$news->newsID}" title="{$news->subject}">{$news->subject}</a></h3>
					<small>{if $news->userID}<a href="{link controller='User' object=$news->getUserProfile()}{/link}" class="userLink" data-user-id="{@$news->userID}">{$news->username}</a>{else}{$news->username}{/if} - {@$news->time|time}</small>
				</div>
			</li>
		{/foreach}
	</ul>
{/if}
