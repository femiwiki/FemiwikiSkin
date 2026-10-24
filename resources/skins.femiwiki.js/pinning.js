// The user options that keep each choice, keyed by the name in data-fw-pin
var OPTIONS = {
	toc: "FemiwikiTocPinned",
	aside: "FemiwikiAsidePinned",
};

/**
 * @param {string} name toc or aside
 * @return {boolean} The element stays beside the content where there is room
 */
function isPinned(name) {
	return document.documentElement.classList.contains(
		`fw-${name}-pinned-clientpref-1`,
	);
}

/**
 * Keeps the ToC or the right column beside the content, or folds it into the drawer, and saves
 * the choice. Logged-out readers keep it in a cookie, as Vector 2022 does, and named users in
 * their options.
 *
 * @param {string} name toc or aside
 * @param {boolean} pinned
 * @return {void}
 */
function setPinned(name, pinned) {
	var option = OPTIONS[/** @type {"toc"|"aside"} */ (name)];
	if (!option || isPinned(name) === pinned) {
		return;
	}
	var value = pinned ? "1" : "0";
	if (mw.user.isNamed()) {
		const classList = document.documentElement.classList;
		classList.remove(`fw-${name}-pinned-clientpref-${pinned ? "0" : "1"}`);
		classList.add(`fw-${name}-pinned-clientpref-${value}`);
		mw.loader.using("mediawiki.api").then(() => {
			new mw.Api().saveOption(option, value);
		});
	} else {
		mw.user.clientPrefs.set(`fw-${name}-pinned`, value);
	}
	mw.hook("skins.femiwiki.pinned").fire(name, pinned);
}

/**
 * Binds the buttons, including those a gadget adds later, by their data-fw-pin and
 * data-fw-pin-value attributes.
 *
 * @return {void}
 */
function init() {
	var tocCheckbox = document.getElementById("fw-toc-checkbox");
	var tocToggle = document.getElementById("fw-toc-toggle");

	document.addEventListener("click", (event) => {
		var target = event.target;
		var button = target instanceof Element && target.closest("[data-fw-pin]");
		if (!(button instanceof HTMLElement)) {
			return;
		}
		var name = button.dataset.fwPin || "";
		var pinned = button.dataset.fwPinValue === "1";
		setPinned(name, pinned);
		if (name !== "toc") {
			return;
		}
		if (tocCheckbox instanceof HTMLInputElement && tocCheckbox.checked) {
			tocCheckbox.checked = false;
			// The checkbox hack updates aria-expanded on this event.
			tocCheckbox.dispatchEvent(new Event("input", { bubbles: true }));
		}
		// The focus goes where the ToC went, as in Vector
		if (!pinned && tocToggle) {
			tocToggle.focus();
		}
	});
}

module.exports = Object.freeze({
	init: init,
	isPinned: isPinned,
	setPinned: setPinned,
});
