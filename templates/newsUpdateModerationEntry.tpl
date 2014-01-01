<article class="message messageReduced">
	<div>
		<section class="messageContent">
			<div>
				<header class="messageHeader">
					<div class="box32">
						<div class="messageHeadline">
							<h1><a href="{link application='news' controller='News' id=$news->newsID}{/link}">{$news->subject}</a></h1>
							<p>
								<span class="username">{if $news->userID}<a href="{link controller='User' object=$news->getUserProfile()}{/link}" class="userLink" data-user-id="{@$news->userID}">{$news->username}</a>{else}{$news->username}{/if}</span>
								{@$news->time|time}
							</p>
						</div>
					</div>
				</header>

				<div class="messageBody">
					<div>
						<div class="messageText">
							{@$news->getFormattedMessage()}
						</div>
					</div>

					<footer class="messageOptions">
						<nav class="jsMobileNavigation buttonGroupNavigation">
							<ul class="smallButtons buttonGroup">
								<li class="toTopLink"><a href="{@$__wcf->getAnchor('top')}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>
							</ul>
						</nav>
					</footer>
				</div>
			</div>
		</section>
	</div>
</article>