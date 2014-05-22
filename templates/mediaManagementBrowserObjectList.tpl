{foreach from=$mediaData[mediaList] item=media}
	<li style="list-style: none;">
		<div class="box32">			
			<div>
				<span class="framed">
					<img src="{@$__wcf->getPath('news')}images/newspictureDummy.png" alt="" />
				</span>
				<div class="containerHeadline">
					<h3>{$media->title}</h3>
					<p><small>{$media->filename}.{$media->fileExtension}</small></p>
				</div>
				

			</div>
		</div>
	</li>
{/foreach}