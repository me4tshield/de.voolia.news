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
					<h3><small><span class="likesBadge badge jsTooltip {if $news->cumulativeLikes > 0}green{elseif $news->cumulativeLikes < 0}red{/if}" title="{lang likes=$news->likes dislikes=$news->dislikes}wcf.like.tooltip{/lang}">{if $news->cumulativeLikes > 0}+{elseif $news->cumulativeLikes == 0}&plusmn;{/if}{#$news->cumulativeLikes}</span></small> <a href="{link application='news' controller='News' object=$news}{/link}" class="newsPreview" data-news-id="{@$news->newsID}" title="{$news->subject}">{$news->subject}</a></h3>
					<small>{implode from=$news->getCategories() item=category}{if $category->isAccessible()}{$category->getTitle()}{/if}{/implode}</small>
				</div>
			</li>
		{/foreach}
	</ul>
{/if}
