<div class="tabMenuContainer" data-active="newsPictureInspectorUpload">
	<nav class="tabMenu">
		<ul>
			<li><a href="#newsPictureInspectorUpload">{lang}news.entry.picture.add{/lang}</a></li>
			{if $categoryID|isset}
				<li><a href="#newsPictureInspectorPicker">{lang}wcf.global.button.upload{/lang}</a></li>
			{/if}
		</ul>
	</nav>

	<div id="newsPictureInspectorUpload" class="container containerPadding tabMenuContent hidden pictureInput">
		<ul>
			{if $picture|isset && !$picture->categoryID}
				<li class="box32 framed">
					<img src="{$picture->getURL()}" alt="" style="width: 32px; max-height: 32px" />
					<div>
						<div>
							<p>{$picture->title}</p>
							<small>{@$picture->filesize|filesize}</small>
						</div>
					</div>
				</li>
			{/if}
		</ul>

		{* placeholder for upload button: *}
		<div id="picturePlaceholder"></div>
		<small>{lang}news.entry.picture.limits{/lang}</small>
	</div>

	{if $categoryID|isset}
		<div id="newsPictureInspectorPicker" class="container containerPadding tabMenuContainer tabMenuContent hidden">
			<nav class="menu">
				<ul>
					{foreach from=$categoryList item=categoryItem}
						<li class="newsPictureInspectorNavigation{if $categoryID == $categoryItem->categoryID} ui-state-active{/if}" data-category-id="{@$categoryItem->categoryID}"><a href="#newsPictureInspector_{@$categoryItem->categoryID}">{$categoryItem->getTitle()}</a></li>
					{/foreach}
				</ul>
			</nav>

			{foreach from=$categoryList item=categoryItem}
				<div id="newsPictureInspector_{@$categoryItem->categoryID}" class="hidden">
					{if $categoryItem->categoryID == $categoryID}
						{include file='groupedPictureList' application='news' category=$categoryItem childCategories=$categoryItem}
					{/if}
				</div>
			{/foreach}
		</div>
	{/if}

	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			// fix anchor
			var $location = location.toString().replace(location.hash, '');
			$('.sitemap .tabMenu a').each(function(index, link) {
				var $link = $(link);
				$link.attr('href', $location + $link.attr('href'));
			});

			WCF.TabMenu.init();
		});
		//]]>
	</script>
</div>
