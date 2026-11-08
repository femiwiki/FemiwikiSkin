(() => {
	"use strict";

	let windowManager, shareDialog;

	// mediawiki.page.ready exports share() from MediaWiki 1.47 on. It opens the
	// browser's share sheet and copies the link with a notification.
	const coreShare = require("mediawiki.page.ready").share;

	const config = require("./config.json");

	/**
	 * @return {string} The URL to share
	 */
	function getShareUrl() {
		const pageId = mw.config.get("wgArticleId");
		if (!config.FemiwikiShareByPageId || !pageId) {
			return window.location.href;
		}
		const url = new URL(mw.config.get("wgScript"), window.location.href);
		url.searchParams.set("curid", String(pageId));
		return url.toString();
	}

	function share() {
		const url = getShareUrl();
		if (coreShare) {
			coreShare({
				url,
				title: mw.config.get("wgPageName").replace(/_/g, " "),
			});
			return;
		}

		// TODO: Remove the code below once MediaWiki 1.47 is required
		if (navigator.share) {
			navigator.share({
				url,
				title: mw.config.get("wgPageName").replace(/_/g, " "),
			});
			return;
		}

		// OOUI windows load with the dialog, not with every page
		mw.loader.using(["skins.femiwiki.share.ui"]).done(() => {
			windowManager = windowManager || OO.ui.getWindowManager();
			if (shareDialog === undefined) {
				shareDialog = new mw.fw.ShareDialog();
				windowManager.addWindows([shareDialog]);
			}

			windowManager.openWindow(shareDialog, {
				url,
				actions: [
					{
						action: "accept",
						label: mw.msg("skin-femiwiki-share-dismiss"),
						flags: "primary",
					},
				],
			});
		});
	}

	function init() {
		// The server-rendered ButtonWidget, handled without infusing it
		const button = document.querySelector(
			"#p-share .oo-ui-buttonElement-button",
		);
		if (!button) {
			return;
		}
		button.addEventListener("click", share);
		button.addEventListener("keydown", (e) => {
			if (e.key === "Enter" || e.key === " ") {
				e.preventDefault();
				share();
			}
		});
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", init);
	} else {
		init();
	}
})();
