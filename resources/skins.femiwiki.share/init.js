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
		const $button = $("#p-share .oo-ui-buttonElement-button");
		$button.on("click", share);
		$button.on("keydown", (e) => {
			if (e.key === "Enter" || e.key === " ") {
				e.preventDefault();
				share();
			}
		});
	}

	$(init);
})();
