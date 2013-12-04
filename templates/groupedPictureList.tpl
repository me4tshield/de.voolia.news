{hascontent}
	{content}
		{if $pictures[$categoryID]|isset}
			<div class="container">
				<ol class="containerList doubleColumned">
					{foreach from=$pictures[$categoryID] item=picture}
						<li class="jsNewsPicture" data-object-id="{@$picture->pictureID}">
							<div class="box32">
								<div class="framed">
									<img src="{$picture->getURL()}" alt="" style="width: 32px" />
								</div>
								<div>
									<p>{$picture->title}</p>
									<small>{@$picture->filesize|filesize}</small>
								</div>
							</div>
						</li>
					{/foreach}
				</ol>
			</div>
		{/if}

		{foreach from=$childCategories item=childCategory}
			{if $pictures[$childCategory->categoryID]|isset}
				<header class="boxHeadline boxSubHeadline">
					<h2>{$childCategory->getTitle()} <span class="badge">{#$pictures[$childCategory->categoryID]|count}</span></h2>
				</header>
				<div class="container marginTop">
					<ol class="containerList doubleColumned">
						{foreach from=$pictures[$childCategory->categoryID] item=picture}
							<li class="jsNewsPicture" data-object-id="{@$picture->pictureID}">
								<div class="box32">
									<div class="framed">
										<img src="{$picture->getURL()}" alt="" style="width: 32px" />
									</div>
									<div>
										<p>{$picture->title}</p>
										<small>{@$picture->filesize|filesize}</small>
									</div>
								</div>
							</li>
						{/foreach}
					</ol>
				</div>
			{/if}
		{/foreach}
	{/content}
{hascontentelse}
	<p class="info">{lang}news.entry.picture.noAvailable{/lang}</p>
{/hascontent}
