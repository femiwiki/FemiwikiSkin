(() => {
	"use strict";

	let windowManager, shareDialog;

	function share() {
		if (navigator.share) {
			navigator.share({
				url: window.location.href,
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
				url: window.location.href,
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
