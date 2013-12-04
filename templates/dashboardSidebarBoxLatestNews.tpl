{if $__wcf->getSession()->getPermission('user.news.canViewNews')}
	<ul class="sidebarBoxList">
		{foreach from=$vooliaNewsList item=vooliaNews}
		<li class="box24">
			{if NEWS_ENABLE_NEWSPICTURE}
				<a href="{link application='news' controller='News' object=$vooliaNews}{/link}" class="framed">
					<img src="{@$vooliaNews->getNewsPicture()->getURL()}" class="newsImageSidebar" alt="" />
				</a>
			{/if}

			<div class="sidebarBoxHeadline">
				<h3><a href="{link application='news' controller='News' object=$vooliaNews}{/link}" class="newsPreview" data-news-id="{@$vooliaNews->newsID}" title="{$vooliaNews->subject}">{$vooliaNews->subject}</a></h3>
				<small><a href="{link controller='User' object=$vooliaNews->getUserProfile()}{/link}" class="userLink" data-user-id="{@$vooliaNews->userID}">{$vooliaNews->username}</a> - {@$vooliaNews->time|time}</small>
			</div>
		</li>
		{/foreach}
	</ul>
{/if}