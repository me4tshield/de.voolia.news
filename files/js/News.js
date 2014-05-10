/**
 * JavaScript for the news plugin.
 * 
 * @author	Pascal Bade <mail@voolia.de>, Florian Frantzen <ray176@voolia.de>
 * @copyright	2013 voolia.de
 * @license	Creative Commons BY-ND <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 * @package	de.voolia.news
 */

/**
 * Initialize the News namespace
 */
var News = {};

/**
 * Namespace for category related actions
 */
News.Category = {};

/**
 * Marks all news categories as read.
 */
News.Category.MarkAllAsRead = Class.extend({
	/**
	 * action proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * Initializes the News.Category.MarkAllAsRead class.
	 */
	init: function() {
		// initialize proxy
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		$('.markAllAsReadButton').click($.proxy(this._click, this));
	},

	/**
	 * Handles the mark all as read button.
	 */
	_click: function() {
		this._proxy.setOption('data', {
			actionName: 'markAllAsRead',
			className: 'news\\data\\category\\NewsCategoryAction'
		});

		this._proxy.sendRequest();
	},

	/**
	 * Marks all categories as read.
	 * 
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		// remove main menu badge
		$('#mainMenu .active .badge').hide();

		// remove the 'newMessageBadge' badge
		$('.newMessageBadge').hide();
	}
});

/**
 * @see	WCF.InlineEditor
 */
News.InlineEditor = WCF.InlineEditor.extend({
	/**
	 * redirect url used when deleting a news
	 * @var	string
	 */
	_redirectURL: '',

	/**
	 * redirect url for adding new updates
	 * @var	string
	 */
	_updateURL: '',

	/**
	 * notification object
	 * @var	WCF.System.Notification
	 */
	_notification: null,

	/**
	 * @see	WCF.InlineEditor.init()
	 */
	init: function(elementSelector, redirectURL, updateURL) {
		this._redirectURL = redirectURL;
		this._updateURL = updateURL;

		this._super(elementSelector);
	},

	/**
	 * @see	WCF.InlineEditor._execute()
	 */
	_execute: function(elementID, optionName, forceEditing, data) {
		if (!this._validate(elementID, optionName)) {
			return false;
		}

		if (optionName == 'edit') {
			window.location = this._getTriggerElement().prop('href');
			return;
		}

		if (optionName == 'update') {
			window.location = this._updateURL;
			return;
		}

		// handle confirmation messages
		if (!forceEditing) {
			switch (optionName) {
				case 'delete':
					var self = this;
					WCF.System.Confirmation.show(WCF.Language.get('news.entry.delete.sure'), function(action) {
						if (action === 'confirm') {
							self._execute(elementID, optionName, true);
						}
					});

					return;

				case 'trash':
					var self = this;
					WCF.System.Confirmation.show(WCF.Language.get('news.entry.trash.confirmMessage'), function(action) {
						if (action === 'confirm') {
							self._execute(elementID, optionName, true, {reason: $('#wcfSystemConfirmationContent').find('textarea').val()});
						}
					}, { }, $('<fieldset><dl><dt>'+ WCF.Language.get('news.entry.trash.reason') +'</dt><dd><textarea cols="40" rows="4" /></dd></dl></fieldset>'));

					return;
			}
		}

		this._proxy.setOption('data', {
			actionName: optionName,
			className: 'news\\data\\news\\NewsAction',
			objectIDs: [ this._elements[elementID].data('objectID') ],
			parameters: {
				data: data
			}
		});
		this._proxy.sendRequest();
	},

	/**
	 * @see	WCF.InlineEditor._getTriggerElement()
	 */
	_getTriggerElement: function() {
		return $('.jsButtonNewsInlineEditor');
	},

	/**
	 * @see	WCF.InlineEditor._setOptions()
	 */
	_setOptions: function() {
		this._options = [
			{ label: WCF.Language.get('wcf.global.button.enable'), optionName: 'enable' },
			{ label: WCF.Language.get('wcf.global.button.disable'), optionName: 'disable' },

			{ label: WCF.Language.get('wcf.global.button.delete'), optionName: 'trash' },
			{ label: WCF.Language.get('news.entry.button.dropdown.restore'), optionName: 'restore' },
			{ label: WCF.Language.get('news.entry.button.dropdown.delete'), optionName: 'delete' },

			{ label: WCF.Language.get('news.entry.button.dropdown.activateComments'), optionName: 'activateComments' },
			{ label: WCF.Language.get('news.entry.button.dropdown.deactivateComments'), optionName: 'deactivateComments' },

			{ optionName: 'divider' },

			{ label: WCF.Language.get('news.entry.button.dropdown.update'), optionName: 'update', isQuickOption: true },
			{ label: WCF.Language.get('wcf.global.button.edit'), optionName: 'edit', isQuickOption: true }
		];
	},

	/**
	 * @see	WCF.InlineEditor._success()
	 */
	_success: function(data, textStatus, jqXHR) {
		this._updateData.push(data);
		this._super(data, textStatus, jqXHR);
	},

	/**
	 * @see	WCF.InlineEditor._updateState()
	 */
	_updateState: function(data) {
		console.log(data);
		var $news = this._elements[this._getTriggerElement().data('elementID')];

		switch (data.actionName) {
			case 'delete':
				window.location = this._redirectURL;
			break;

			case 'restore':
			case 'trash':
				$news.toggleClass('messageDeleted').data('isDeleted', ((data.actionName == 'trash') ? 1 : 0));
			break;

			case 'enable':
			case 'disable':
				$news.toggleClass('messageDisabled').data('isActive', ((data.actionName == 'enable') ? 1 : 0));
			break;

			case 'activateComments':
			case 'deactivateComments':
				this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success.edit'));
				this._notification.show(function() { window.location.reload(); });
			break;
		}
	},

	/**
	 * @see	WCF.InlineEditor._validate()
	 */
	_validate: function(elementID, optionName) {
		var $news = this._elements[elementID];

		switch (optionName) {
			case 'enable':
			case 'disable':
				// it's not possible to enable/disable deleted news
				if($news.data('isDeleted')) return false;

				if (!$news.data('isActive')) {
					return (optionName == 'enable');
				} else {
					return (optionName == 'disable');
				}
			break;

			case 'activateComments':
			case 'deactivateComments':
				if ($news.data('canManageComments') == 'false') return false;

				if ($news.data('isCommentable')) {
					return (optionName == 'deactivateComments');
				} else {
					return (optionName == 'activateComments');
				}
			break;

			case 'delete':
			case 'restore':
			case 'trash':
				if ($news.data('canDelete') == 'false') return false;

				if ($news.data('isDeleted')) {
					return (optionName == 'delete' || optionName == 'restore');
				} else {
					return (optionName == 'trash');
				}
			break;

			case 'update':
			case 'edit':
				return true;
			break;
		}

		// can't handle option
		return false;
	}
});

/**
 * Like support for news entries.
 * 
 * @see	WCF.Like
 */
News.Like = WCF.Like.extend({
	/**
	 * @see	WCF.Like._getContainers()
	 */
	_getContainers: function() {
		return $('article');
	},

	/**
	 * @see	WCF.Like._getObjectID()
	 */
	_getObjectID: function(containerID) {
		return this._containers[containerID].data('objectID');
	},

	/**
	 * @see	WCF.Like._getWidgetContainer()
	 */
	_getWidgetContainer: function(containerID) {
		return this._containers[containerID].find('.messageHeader');
	},

	/**
	 * @see	WCF.Like._buildWidget()
	 */
	_buildWidget: function(containerID, likeButton, dislikeButton, badge, summary) {
		var $widgetContainer = this._getWidgetContainer(containerID);
		if (this._canLike) {
			var $smallButtons = this._containers[containerID].find('.newsSmallButtons');
			likeButton.insertBefore($smallButtons.find('.toTopLink'));
			dislikeButton.insertBefore($smallButtons.find('.toTopLink'));
			dislikeButton.find('a').addClass('button');
			likeButton.find('a').addClass('button');
		}

		if (summary) {
			summary.appendTo(this._containers[containerID].find('.messageBody > .messageFooter'));
			summary.addClass('messageFooterNote');
		}
		$widgetContainer.find('.permalink').after(badge);
	},

	/**
	 * Sets button active state.
	 * 
	 * @param 	jquery		likeButton
	 * @param 	jquery		dislikeButton
	 * @param	integer		likeStatus
	 */
	_setActiveState: function(likeButton, dislikeButton, likeStatus) {
		likeButton = likeButton.find('.button').removeClass('active');
		dislikeButton = dislikeButton.find('.button').removeClass('active');

		if (likeStatus == 1) {
			likeButton.addClass('active');
		} else if (likeStatus == -1) {
			dislikeButton.addClass('active');
		}
	},

	/**
	 * @see	WCF.Like._addWidget()
	 */
	_addWidget: function(containerID, widget) {}
});

/**
 * Shows a preview popover when a user hovers over a news subject with the class 'newsPreview'.
 * 
 * @see	WCF.Popover
 */
News.Preview = WCF.Popover.extend({
	/**
	 * action proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * @see	WCF.Popover.init()
	 */
	init: function() {
		this._super('.newsPreview');

		// init proxy
		this._proxy = new WCF.Action.Proxy({
			showLoadingOverlay: false
		});

		WCF.DOMNodeInsertedHandler.addCallback('News.Preview', $.proxy(this._initContainers, this));
	},

	/**
	 * @see	WCF.Popover._loadContent()
	 */
	_loadContent: function() {
		var $link = $('#' + this._activeElementID);

		this._proxy.setOption('data', {
			actionName: 'getNewsPreview',
			className: 'news\\data\\news\\NewsAction',
			objectIDs: [ $link.data('newsID') ]
		});

		var $elementID = this._activeElementID;
		var self = this;

		this._proxy.setOption('success', function(data, textStatus, jqXHR) {
			self._insertContent($elementID, data.returnValues.template, true);
		});

		this._proxy.sendRequest();
	}
});

/**
 * Provides the quote manager for news entries.
 *
 * @param	WCF.Message.Quote.Manager	quoteManager
 * @see		WCF.Message.Quote.Handler
 */
News.QuoteHandler = WCF.Message.Quote.Handler.extend({
	/**
	 * @see	WCF.Message.QuoteManager.init()
	 */
	init: function(quoteManager) {
		this._super(quoteManager, 'news\\data\\news\\NewsAction', 'de.voolia.news.entry', '.message', '.messageBody', '.messageBody > div > div.messageText');
	}
});

/**
 * Namespace for news source related actions.
 */
News.Source = {};

/**
 * Handles news source managment.
 * @param	string		containerID
 * @param	array<object>	sourceList
 */
News.Source.Managment = Class.extend({
	/**
	 * container object
	 * @var	jQuery
	 */
	_container: null,

	/**
	 * number of sources
	 * @var	integer
	 */
	_count: 0,

	/**
	 * width for input-elements
	 * @var	integer
	 */
	_inputSize: 0,

	/**
	 * maximum allowed number of sources
	 * @var	integer
	 */
	_maxCount: 0,

	/**
	 * Initializes the News.Source.Management class.
	 * @param	string		containerID
	 * @param	array<object>	sourceList
	 * @param	integer		maxCount
	 */
	init: function(containerID, sourceList, maxCount) {
		this._maxCount = maxCount || 0;
		this._container = $('#' + containerID).children('ol:eq(0)');
		if (!this._container.length) {
			console.debug("[News.Source.Management] Invalid container id given, aborting.");
			return;
		}

		sourceList = sourceList || [ ];
		this._createSourceList(sourceList);

		// bind event listener
		$(window).resize($.proxy(this._resize, this));

		// init sorting
		if (this._maxCount != 1) {
			new WCF.Sortable.List(containerID, '', undefined, undefined, true);
		}

		// trigger resize event for field length calculation
		this._resize();
	},

	/**
	 * creates the inputs for a source
	 * @param	string		sourceText
	 * @param	string		sourceLink
	 * @param	jQuery		insertAfter
	 */
	_createSource: function(sourceText, sourceLink, insertAfter) {
		if (this._count && this._count == this._maxCount) {
			return false;
		}

		sourceText = sourceText || '';
		sourceLink = sourceLink || '';
		insertAfter = insertAfter || null;

		var $listItem = $('<li />');
		if (this._maxCount != 1) $listItem.addClass('sortableNode');
		if (insertAfter === null) {
			$listItem.appendTo(this._container);
		} else {
			$listItem.insertAfter(insertAfter);
		}

		// insert buttons
		if (this._maxCount != 1) {
			var $buttonContainer = $('<span class="sortableButtonContainer" />').appendTo($listItem);
			$('<span class="icon icon16 icon-plus jsTooltip jsAddSource pointer" title="' + WCF.Language.get('news.entry.add.informations.button.sources.addSource') + '" />').click($.proxy(this._addSource, this)).appendTo($buttonContainer);
			$('<span class="icon icon16 icon-remove jsTooltip jsDeleteSource pointer" title="' + WCF.Language.get('news.entry.add.informations.button.sources.removeSource') + '" />').click($.proxy(this._removeSource, this)).appendTo($buttonContainer);
		}

		// insert input field
		var $textInput = $('<input type="text" name="sourceText[]" class="jsSourceText" value="' + sourceText + '" maxlength="2048" placeholder="' + WCF.Language.get('news.entry.add.informations.sources.input.title') + '" />').css({ width: this._inputSize + "px" }).keydown($.proxy(this._keyDown, this)).appendTo($listItem);
		var $linkInput = $('<input type="text" name="sourceLink[]" class="jsSourceLink" value="' + sourceLink + '" maxlength="2048" placeholder="' + WCF.Language.get('news.entry.add.informations.sources.input.link') + '" />').css({ width: this._inputSize + "px" }).keydown($.proxy(this._keyDown, this)).appendTo($listItem);

		if (insertAfter !== null) {
			$textInput.focus();
		}

		WCF.DOMNodeInsertedHandler.execute();

		this._count++;
		if (this._count && this._count == this._maxCount) {
			this._container.find('span.jsAddSource').removeClass('pointer').addClass('disabled');
		}
	},

	/**
	 * Creates the source list on init.
	 * @param	array<object>		sourceList
	 */
	_createSourceList: function(sourceList) {
		for (var $i = 0, $length = sourceList.length; $i < $length; $i++) {
			var $source = sourceList[$i];
			this._createSource($source.sourceText, $source.sourceLink);
		}

		// add empty source
		this._createSource();
	},

	/**
	 * Handles key down events for source input field.
	 * @param	object		event
	 * @return	boolean
	 */
	_keyDown: function(event) {
		// ignore every key except for [Enter]
		if (event.which !== 13) {
			return true;
		}

		$(event.currentTarget).prev('.sortableButtonContainer').children('.jsAddSource').trigger('click');

		event.preventDefault();
		event.stopPropagation();
		return false;
	},

	/**
	 * Adds a new source after current one.
	 * @param	object		event
	 */
	_addSource: function(event) {
		var $listItem = $(event.currentTarget).parents('li');
		this._createSource(undefined, undefined, $listItem);
	},

	/**
	 * Removes a source.
	 * @param	object		event
	 */
	_removeSource: function(event) {
		$(event.currentTarget).parents('li').remove();

		this._count--;
		this._container.find('span.jsAddSource').addClass('pointer').removeClass('disabled');

		if (this._container.children('li').length == 0) {
			this._createSource();
		}
	},

	/**
	 * Handles the 'resize'-event to adjust input-width.
	 */
	_resize: function() {
		var $containerWidth = this._container.innerWidth();

		// select first source to determine dimensions
		var $listItem = this._container.children('li:eq(0)');
		var $sourceTextInput = $listItem.children('.jsSourceText');
		var $buttonWidth = $listItem.children('.sortableButtonContainer').outerWidth();
		var $inputSize = Math.floor(($containerWidth - $buttonWidth - $sourceTextInput.outerWidth(true) + $sourceTextInput.width()) / 2);

		if ($inputSize != this._inputSize) {
			this._inputSize = $inputSize;

			// update width of <input /> elements
			this._container.find('li > input').css({ width: this._inputSize + 'px' });
		}
	}
});
