(function() {
	/**
	 * Enables the media management plugin.
	 */
	CKEDITOR.plugins.add('vooliaNewsPlugin', {
		_mediaBrowser: null,
		
		/**
		 * list of required plugins
		 * @var	array<string>
		 */
		requires: [ 'button' ],
		
		/**
		 * Initializes the plugin.
		 * 
		 * @param	CKEDITOR	editor
		 */
		init: function(editor) {
			var $commandName = '__news_openMediaManagementBrowser';
			var self = this;
			editor.addCommand($commandName, {
				exec: function(editor) {
					if (self._mediaBrowser === null) {
						self._mediaBrowser = new News.MediaManagement.Browser(editor);
					}
					
					self._mediaBrowser.open();
				}
			});
			editor.ui.addButton($commandName, {
				label: WCF.Language.get('news.mediaManagement.title'),
				icon: '../../../icon/newsMediaManagementBrowser.png',
				command: $commandName
			});
		}
	});
})();
