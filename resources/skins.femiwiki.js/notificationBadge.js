function init() {
	// Notification badge
	const echoBadge = document.querySelector(
		"#pt-notifications-all .mw-echo-notifications-badge",
	);
	const badge = parseInt(
		(echoBadge && echoBadge.getAttribute("data-counter-num")) || "",
		10,
	);
	const menuBadge = document.querySelector("#fw-menu-toggle .badge");
	if (menuBadge && !Number.isNaN(badge) && badge !== 0) {
		menuBadge.classList.add("active");
		menuBadge.textContent = badge > 10 ? "+9" : String(badge);
	}
}

module.exports = Object.freeze({ init });
