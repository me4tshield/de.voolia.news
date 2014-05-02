{if $__wcf->getSession()->getPermission('user.news.canViewNews')}
	<ul class="sidebarBoxList dashboardSidebarBoxMostLikedNews">
		{foreach from=$vooliaNewsList item=vooliaNews}
			<li{if NEWS_ENABLE_NEWSPICTURE} class="box24"{/if}>
				{if NEWS_ENABLE_NEWSPICTURE}
					<a href="{link application='news' controller='News' object=$vooliaNews}{/link}" class="framed">
						<img src="{@$vooliaNews->getNewsPicture()->getURL()}" class="newsImageSidebar" alt="" />
					</a>
				{/if}

				<div class="sidebarBoxHeadline">
					<h3><small><span class="likesBadge badge jsTooltip {if $vooliaNews->cumulativeLikes > 0}green{elseif $vooliaNews->cumulativeLikes < 0}red{/if}" title="{lang likes=$vooliaNews->likes dislikes=$vooliaNews->dislikes}wcf.like.tooltip{/lang}">{if $vooliaNews->cumulativeLikes > 0}+{elseif $vooliaNews->cumulativeLikes == 0}&plusmn;{/if}{#$vooliaNews->cumulativeLikes}</span></small> <a href="{link application='news' controller='News' object=$vooliaNews}{/link}" class="newsPreview" data-news-id="{@$vooliaNews->newsID}" title="{$vooliaNews->subject}">{$vooliaNews->subject}</a></h3>
					<small>{implode from=$vooliaNews->getCategories() item=category}{if $category->isAccessible()}{$category->getTitle()}{/if}{/implode}</small>
				</div>
			</li>
		{/foreach}
	</ul>
{/if}
