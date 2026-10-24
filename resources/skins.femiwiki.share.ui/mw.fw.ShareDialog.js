(() => {
	"use strict";

	/**
	 * @class ShareDialog
	 */
	mw.fw.ShareDialog = function MwFwShareDialog(config) {
		// Configuration initialization
		config = config || {};

		// Parent constructor
		mw.fw.ShareDialog.super.call(this, config);
	};

	// Initialization
	OO.inheritClass(mw.fw.ShareDialog, OO.ui.MessageDialog);

	mw.fw.ShareDialog.static.name = "ShareDialog";

	mw.fw.ShareDialog.prototype.initialize = function () {
		mw.fw.ShareDialog.super.prototype.initialize.call(this);

		// Make UI elements
		this.content = new OO.ui.PanelLayout({
			padded: true,
			expanded: false,
		});
		this.$element.addClass("mw-fw-ui-shareDialog");

		// Make SNS Buttons
		const items = [];
		this.twitterButton = new OO.ui.ButtonWidget({
			framed: false,
			icon: "newWindow",
			label: mw.msg("skin-femiwiki-share-twitter"),
		});
		items.push(this.twitterButton);
		this.twitterButton.$element.addClass("mw-fw-ui-twitterButton");
		this.mediaButtonGroup = new OO.ui.ButtonGroupWidget({
			items,
		});

		// Create a TextForm to copy
		this.urlWidget = new OO.ui.TextInputWidget({
			focusable: true,
			readOnly: true,
		});

		// Connect onClick function
		this.urlWidget.$element.on("click", () => {
			this.urlWidget.select();
		});

		// Append elements
		this.content.$element.append(this.mediaButtonGroup.$element);
		this.content.$element.append(this.urlWidget.$element);
		this.$body.append(this.content.$element);
	};

	mw.fw.ShareDialog.prototype.getSetupProcess = function (data) {
		data = data || {};

		return mw.fw.ShareDialog.super.prototype.getSetupProcess
			.call(this, data)
			.next(() => {
				if (this.longUrl !== data.url) {
					this.longUrl = data.url;
					this.updateUrl(this.longUrl);
				}
			});
	};

	mw.fw.ShareDialog.prototype.getReadyProcess = function (data) {
		data = data || {};
		return mw.fw.ShareDialog.super.prototype.getReadyProcess
			.call(this, data)
			.next(() => {
				this.urlWidget.select();
			});
	};

	mw.fw.ShareDialog.prototype.updateUrl = function (url) {
		this.urlWidget.setValue(url);

		const pageName = mw.config.get("wgPageName").replace(/_/g, " ");
		const tweet = `${pageName} ${url} #${mw.config.get("wgSiteName")}`;

		this.twitterButton.setHref(
			`https://twitter.com/intent/tweet?text=${encodeURIComponent(tweet)}`,
		);
	};

	mw.fw.ShareDialog.prototype.getBodyHeight = function () {
		return this.content.$element.outerHeight(true);
	};

	mw.fw.ShareDialog.prototype.getBodyWidth = function () {
		return this.content.$element.outerWidth(true);
	};
})();
