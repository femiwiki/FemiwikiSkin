(() => {
	"use strict";

	function init() {
		OO.ui.infuse($("#p-share")).on("click", () => {
			if (navigator.share) {
				navigator.share({
					url: window.location.href,
					title: mw.config.get("wgPageName").replace(/_/g, " "),
				});
			} else {
				let windowManager, shareDialog;

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
		});
	}

	$(init);
})();
