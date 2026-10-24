/** @type {CheckboxHack} */ var checkboxHack = require(
	/** @type {string} */ ("mediawiki.page.ready"),
).checkboxHack;

/**
 * Improve the interactivity of the sidebar panel by binding optional checkbox hack enhancements
 * for focus and `aria-expanded`. Also, flip the icon image on click.
 *
 * @param {HTMLElement|null} checkbox
 * @param {HTMLElement|null} button
 * @return {void}
 */
function initCheckboxHack(checkbox, button) {
	if (checkbox instanceof HTMLInputElement && button) {
		checkboxHack.bindToggleOnClick(checkbox, button);
		checkboxHack.bindUpdateAriaExpandedOnInput(checkbox);
		checkboxHack.updateAriaExpanded(checkbox);
		checkboxHack.bindToggleOnEnter(checkbox);
	}
}

/**
 * Close the menu on a click or focus outside it, as Vector's dropdowns do with core's
 * checkboxHack.bind.
 *
 * @param {HTMLElement|null} checkbox
 * @param {HTMLElement|null} button
 * @param {HTMLElement|null} menu
 * @return {void}
 */
function initDropdown(checkbox, button, menu) {
	if (checkbox instanceof HTMLInputElement && button && menu) {
		checkboxHack.bind(window, checkbox, button, menu);
		checkboxHack.updateAriaExpanded(checkbox);
	}
}

/**
 * The site menu closes like initDropdown, except that Echo's flyout counts as inside it. The
 * flyout opens from a link in the menu but is drawn on the body, so core's dismissal would close
 * the menu, and the flyout with it, as it opens.
 *
 * @param {HTMLElement|null} checkbox
 * @param {HTMLElement|null} button
 * @param {HTMLElement|null} menu
 * @return {void}
 */
function initSiteMenu(checkbox, button, menu) {
	if (!(checkbox instanceof HTMLInputElement && button && menu)) {
		return;
	}
	initCheckboxHack(checkbox, button);

	/**
	 * @param {Event} event
	 * @return {void}
	 */
	function dismissIfOutside(event) {
		var node = event.target;
		if (
			checkbox instanceof HTMLInputElement &&
			checkbox.checked &&
			node instanceof Element &&
			!checkbox.contains(node) &&
			!(button && button.contains(node)) &&
			!(menu && menu.contains(node)) &&
			!node.closest(".mw-echo-ui-overlay")
		) {
			checkbox.checked = false;
			// checkboxHack updates aria-expanded on this event.
			checkbox.dispatchEvent(new Event("input", { bubbles: true }));
		}
	}
	window.addEventListener("click", dismissIfOutside, true);
	window.addEventListener("focusin", dismissIfOutside, true);
}

/**
 * @return {void}
 */
function main() {
	require("./discussionStatus.js").init();
	require("./notificationBadge.js").init();
	require("./watchingUsers.js").init();
	require("./tableOfContents.js").init();
	require("./navBar.js").init();

	initCheckboxHack(
		window.document.getElementById("fw-toc-checkbox"),
		window.document.getElementById("fw-toc-toggle"),
	);
	initCheckboxHack(
		window.document.getElementById("fw-special-nav-checkbox"),
		window.document.getElementById("fw-special-nav-toggle"),
	);
	initSiteMenu(
		window.document.getElementById("fw-menu-checkbox"),
		window.document.getElementById("fw-menu-toggle"),
		window.document.getElementById("fw-menu"),
	);
	initDropdown(
		window.document.getElementById("fw-page-menu-checkbox"),
		window.document.getElementById("p-menu-toggle"),
		window.document.getElementById("p-actions-and-toolbox"),
	);

	var fwMenuToggle = document.querySelector("#fw-menu-toggle");
	var fwNotificationBadge = document.querySelector("#fw-menu-toggle .badge");
	if (fwMenuToggle && fwNotificationBadge) {
		fwMenuToggle.addEventListener("click", () => {
			// @ts-expect-error: fwNotificationBadge is possibly 'null'.
			fwNotificationBadge.classList.remove("active");
		});
	}
}

main();
