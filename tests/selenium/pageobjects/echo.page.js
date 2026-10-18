import Page from "wdio-mediawiki/Page.js";

class EchoPage extends Page {
	get notifications() {
		return $("#pt-notifications-all");
	}
	get popup() {
		return $(".mw-echo-ui-notificationBadgeController-popup");
	}
}

export default new EchoPage();
