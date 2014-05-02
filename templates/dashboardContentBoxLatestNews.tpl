{if $__wcf->getSession()->getPermission('user.news.canViewNews') && $vooliaNewsList|count}
	<script data-relocate="true" type="text/javascript">
		//<![CDATA[
			$(function() {
				// Implements the wcfSlideshow() for news entries
				$('.newsSlideshowContainer').wcfSlideshow({
					cycleInterval: {NEWS_DASHBOARD_SLIDESHOW_INTERVAL}
				})
			});
		//]]>
	</script>

	<div class="container containerPadding marginTop dashboardContentBoxLatestNews">
		<div class="slideshowContainer newsSlideshowContainer">
			<ul>
				{foreach from=$vooliaNewsList item=vooliaNews}
				<li class="newsSlideshow">
					{if NEWS_ENABLE_NEWSPICTURE}
						<div class="box32">
							<a href="{link application='news' controller='News' object=$vooliaNews}{/link}" class="framed">
								<img src="{@$vooliaNews->getNewsPicture()->getURL()}" class="newsImage" alt="" />
							</a>
					{/if}

					<div>
						<div class="containerHeadline">
							<h3><a href="{link application='news' controller='News' object=$vooliaNews}{/link}" class="newsPreview" data-news-id="{@$vooliaNews->newsID}" title="{$vooliaNews->subject}">{$vooliaNews->subject}</a></h3>
							<p><small><span class="username"><a href="{link controller='User' object=$vooliaNews->getUserProfile()}{/link}" class="userLink" data-user-id="{$vooliaNews->userID}">{$vooliaNews->username}</a></span> - {@$vooliaNews->time|time}{if $vooliaNews->isCommentable()} - {lang}news.dashboard.box.hotnews.comments{/lang}{/if}</small></p>
						</div>
					</div>

					{if NEWS_ENABLE_NEWSPICTURE}</div>{/if}
				</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}