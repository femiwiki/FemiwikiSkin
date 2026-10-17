function init() {
	// Notification badge
	var badge = parseInt(
		$("#pt-notifications-all .mw-echo-notifications-badge").attr(
			"data-counter-num",
		) || "",
		10,
	);
	if (!Number.isNaN(badge) && badge !== 0) {
		$("#fw-menu-toggle .badge")
			.addClass("active")
			.text(badge > 10 ? "+9" : badge);
	}
}

module.exports = Object.freeze({ init: init });
