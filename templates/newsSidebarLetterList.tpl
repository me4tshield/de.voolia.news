{assign var=encodedLetter value=$letter|rawurlencode}
<fieldset>
	<legend>{lang}news.sidebar.letterList.title{/lang}</legend>
	<ul class="letters buttonList smallButtons">
		{foreach from=$letters item=__letter}
			<li><a href="{link application='news' controller='NewsArchive'}categoryID={$categoryID}&pageNo={@$pageNo}&letter={$__letter|rawurlencode}{/link}" class="button small{if $letter == $__letter} active{/if}">{$__letter}</a></li>
		{/foreach}
		{if !$letter|empty}<li><a href="{link application='news' controller='NewsArchive'}categoryID={$categoryID}&pageNo={@$pageNo}{/link}" class="button small">{lang}wcf.user.members.sort.letters.all{/lang}</a></li>{/if}
	</ul>
</fieldset>