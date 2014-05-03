/**
 * Javascript for the news pictures.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 voolia.de
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.voolia.news
 */

/**
 * Initialize the namespace for news picture related actions.
 */
if (!News) var News = {};
News.Picture = {};

/**
 * News picture managment.
 */
News.Picture.Managment = Class.extend({
	/**
	 * picture category cache
	 * @var	array
	 */
	_cache: [ ],

	/**
	 * dialog overlay
	 * @var	jQuery
	 */
	_dialog: null,

	/**
	 * initialization state
	 * @var	boolean
	 */
	_didInit: false,

	/**
	 * input field saving the selected picture.
	 * @var	jQuery
	 */
	_inputField: null,

	/**
	 * inspector button opening the dialog.
	 * @var	jQuery
	 */
	_inspectorButton: null,

	/**
	 * action proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * Initializes the News.Picture.Managment class.
	 * 
	 * @param	jQuery		inspectorButton
	 * @param	jQuery		inputField
	 */
	init: function(inspectorButton, inputField) {
		this._inspectorButton = inspectorButton;
		this._inputField = inputField;

		this._inspectorButton.click($.proxy(this._click, this));

		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
	},

	/**
	 * Handles clicks on news picture inspector buttons.
	 * 
	 * @param	object		event
	 */
	_click: function(event) {
		var $button = $(event.currentTarget);
		this._currentInspectorButton = $button;

		if (this._dialog === null) {
			this._dialog = $('<div id="newsPictureDialog" />').appendTo(document.body);

			this._proxy.setOption('data', {
				actionName: 'getPictureList',
				className: 'news\\data\\news\\picture\\NewsPictureAction',
				parameters: {
					pictureID: this._inputField.val()
				}
			});
			this._proxy.sendRequest();
		} else {
			this._dialog.wcfDialog('open');
		}
	},

	/**
	 * Navigates between different categories.
	 * 
	 * @param	object		event
	 */
	_navigate: function(event) {
		var $categoryID = $(event.currentTarget).data('categoryID');
		if (WCF.inArray($categoryID, this._cache)) {
			this._dialog.find('.tabMenuContainer').wcfTabs('select', 'newsPictureInspector_'+ $categoryID);

			// redraw dialog
			this._dialog.wcfDialog('render');
		} else {
			this._proxy.setOption('data', {
				actionName: 'getPictureList',
				className: 'news\\data\\news\\picture\\NewsPictureAction',
				parameters: {
					categoryID: $categoryID
				}
			});
			this._proxy.sendRequest();
		}
	},

	/**
	 * Handles clicks on news pictures within the dialog.
	 * 
	 * @param	object		event
	 */
	_pictureClick: function(event) {
		var $picture = $(event.currentTarget);
		this._inputField.val($picture.data('objectID'));

		// update displayed picture
		$picture.clone().appendTo($('.pictureInput ul').html(''));

		// close dialog
		this._dialog.wcfDialog('close');
	},

	/**
	 * Handles successful AJAX responses.
	 * 
	 * @param	objectID	data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		// mark category as loaded
		this._cache.push(data.returnValues.categoryID);

		if (this._didInit) {
			this._dialog.find('#newsPictureInspector_' + data.returnValues.categoryID).html(data.returnValues.template);

			// redraw dialog
			this._dialog.wcfDialog('render');
		} else {
			this._dialog.html(data.returnValues.template);

			// bind event listener
			this._dialog.find('.newsPictureInspectorNavigation').click($.proxy(this._navigate, this));

			// select active item
			this._dialog.find('.tabMenuContainer').wcfTabs('select', 'newsPictureInspector_' + data.returnValues.categoryID);

			// init upload button
			test = new News.Picture.Upload();

			// show dialog
			this._dialog.wcfDialog({
				title: WCF.Language.get('news.entry.picture.button.choose')
			});

			this._didInit = true;
		}

		this._dialog.find('.jsNewsPicture').click($.proxy(this._pictureClick, this));
		this._dialog.find('.jsNewsPicture[data-object-id="'+ this._inputField.val() +'"]').addClass('active');
	}
});

/**
 * News picture upload function
 * 
 * @see	WCF.Upload
 */
News.Picture.Upload = WCF.Upload.extend({
	/**
	 * Initalizes a new News.Picture.Upload object.
	 */
	init: function() {
		this._super($('#picturePlaceholder'), $('.pictureInput ul'), 'news\\data\\news\\picture\\NewsPictureAction');
	},

	/**
	 * @see	WCF.Upload._initFile()
	 */
	_initFile: function(file) {
		this._fileListSelector.children('li').remove();

		var $li = $('<li class="box32"><span class="icon icon32 icon-spinner" /><div><div><p>'+ file.name +'</p><small><progress max="100"></progress></small></div></div></li>');

		this._fileListSelector.append($li);
		this._fileListSelector.show();
		return $li;
	},

	/**
	 * @see	WCF.Upload._success()
	 */
	_success: function(uploadID, data) {
		var $li = this._fileListSelector.find('li');

		// remove progress bar
		$li.find('progress').remove();

		if (data.returnValues.pictureID) {
			// update icon
			$li.children('.icon-spinner').remove();
			$li.prepend('<div class="framed"><img src="'+ data.returnValues.url +'" alt="" style="width: 32px; max-height: 32px" /></div>');

			// update file size
			$li.find('small').append(data.returnValues['formattedFilesize']);

			// save upload id
			$('#pictureID').val(data.returnValues.pictureID);

			// show success message
			var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
			$notification.show();
		} else {
			// update icon
			$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');

			$li.find('div > div').append($('<small class="innerError">'+WCF.Language.get('news.entry.picture.error.' + data.returnValues.errorType)+'</small>'));
			$li.addClass('uploadFailed');
		}

		// fix webkit rendering bug
		$li.css('display', 'block');

		WCF.DOMNodeInsertedHandler.execute();
	},

	/**
	 * @see	WCF.Upload._error()
	 */
	_error: function() {
		// mark uploads as failed
		var $listItem = this._fileListSelector.find('li');
		$listItem.addClass('uploadFailed').children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');
		$listItem.find('div > div').append($('<small class="innerError">'+WCF.Language.get('news.entry.picture.error.uploadFailed')+'</small>'));
	}
});
