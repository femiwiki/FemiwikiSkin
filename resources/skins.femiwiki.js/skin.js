/** @type {CheckboxHack} */ const checkboxHack = require(
	/** @type {string} */ ("mediawiki.page.ready"),
).checkboxHack;

/**
 * Lets the keyboard work a label that stands in for a checkbox hack button. Core's checkboxHack
 * leaves the focus to the checkbox, but this skin hides the checkbox and focuses the label, so
 * Enter and Space would do nothing (femiwiki/femiwiki#148). Escape closes what the label opened
 * and brings the focus back to it.
 *
 * A drawer comes later in the page than its button, so the focus moves into it when it opens.
 *
 * @param {HTMLInputElement} checkbox
 * @param {HTMLElement} button
 * @param {HTMLElement|null} [drawer]
 * @return {void}
 */
function bindKeyboard(checkbox, button, drawer) {
	function updateAriaExpanded() {
		button.setAttribute("aria-expanded", String(checkbox.checked));
	}
	updateAriaExpanded();
	checkbox.addEventListener("input", updateAriaExpanded);

	button.addEventListener("keydown", (event) => {
		if (event.key !== "Enter" && event.key !== " ") {
			return;
		}
		// Space would scroll the page
		event.preventDefault();
		if (event.repeat) {
			return;
		}
		button.click();
		if (!drawer || !checkbox.checked) {
			return;
		}
		// The first control on show, as the drawer hides some of its buttons
		const first = Array.from(drawer.querySelectorAll("a[href], button")).find(
			(element) => element.getClientRects().length > 0,
		);
		if (first instanceof HTMLElement) {
			first.focus();
		}
	});

	window.addEventListener("keydown", (event) => {
		if (event.key === "Escape" && checkbox.checked) {
			checkbox.checked = false;
			checkbox.dispatchEvent(new Event("input", { bubbles: true }));
			button.focus();
		}
	});
}

/**
 * Improve the interactivity of the sidebar panel by binding optional checkbox hack enhancements
 * for focus and `aria-expanded`. Also, flip the icon image on click.
 *
 * @param {HTMLElement|null} checkbox
 * @param {HTMLElement|null} button
 * @param {HTMLElement|null} [drawer]
 * @return {void}
 */
function initCheckboxHack(checkbox, button, drawer) {
	if (checkbox instanceof HTMLInputElement && button) {
		checkboxHack.bindToggleOnClick(checkbox, button);
		checkboxHack.bindUpdateAriaExpandedOnInput(checkbox);
		checkboxHack.updateAriaExpanded(checkbox);
		checkboxHack.bindToggleOnEnter(checkbox);
		bindKeyboard(checkbox, button, drawer);
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
		bindKeyboard(checkbox, button);
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
		const node = event.target;
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
	require("./pinning.js").init();
	require("./navBar.js").init();

	initCheckboxHack(
		window.document.getElementById("fw-toc-checkbox"),
		window.document.getElementById("fw-toc-toggle"),
		window.document.getElementById("fw-toc"),
	);
	initCheckboxHack(
		window.document.getElementById("fw-special-nav-checkbox"),
		window.document.getElementById("fw-special-nav-toggle"),
		window.document.getElementById("p-associated-pages"),
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

	const fwMenuToggle = document.querySelector("#fw-menu-toggle");
	const fwNotificationBadge = document.querySelector("#fw-menu-toggle .badge");
	if (fwMenuToggle && fwNotificationBadge) {
		fwMenuToggle.addEventListener("click", () => {
			fwNotificationBadge.classList.remove("active");
		});
	}
}

main();
