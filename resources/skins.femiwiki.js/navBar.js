const PINNED_CLASS = "fw-nav-bar-pinned";
const SHOWN_CLASS = "fw-nav-bar-shown";
const ANIMATE_CLASS = "fw-nav-bar-animate";
const TOC_CLASS = "fw-nav-bar-toc";
// Codex @min-width-breakpoint-desktop, from where the ToC column follows the scroll instead
const TOC_COLUMN_QUERY = "(min-width: 1120px)";
// Long enough for the scroll that a click on a section link starts
const JUMP_MS = 200;

/**
 * Brings the top bar back when the reader scrolls up, where the ToC is a drawer (#503).
 *
 * The bar keeps its place in the page and is pinned only while out of view, so pinning moves
 * nothing. Like Vector's sticky header it slides with a transform, and scroll-padding-top keeps
 * section links clear of it. A jump to a section is not a scroll up, so it hides the bar.
 *
 * @return {void}
 */
function init() {
	const bar = document.querySelector(".nav-bar");
	const action = mw.config.get("wgAction");
	if (
		!(bar instanceof HTMLElement) ||
		action === "edit" ||
		action === "submit"
	) {
		return;
	}
	const navBar = bar;
	const classList = document.body.classList;
	const toColumn = window.matchMedia(TOC_COLUMN_QUERY);
	const search = document.getElementById("p-search");
	const menuCheckbox = document.getElementById("fw-menu-checkbox");
	const tocCheckbox = document.getElementById("fw-toc-checkbox");
	const tocToggle = document.getElementById("fw-nav-toc-toggle");
	const titleButtons = document.getElementById("p-title-buttons");
	const height = navBar.offsetHeight;
	let lastY = window.scrollY;
	let upDistance = 0;
	let jumped = false;
	let paused = false;
	let frame = 0;

	/**
	 * @return {void}
	 */
	function unpin() {
		classList.remove(PINNED_CLASS, SHOWN_CLASS, ANIMATE_CLASS);
	}

	/**
	 * @return {void}
	 */
	function show() {
		if (!classList.contains(PINNED_CLASS)) {
			classList.add(PINNED_CLASS);
			// Lay the bar out above the screen first, so it slides in from there
			navBar.getBoundingClientRect();
			classList.add(ANIMATE_CLASS);
		}
		// The bar is out of sight here, so the button comes and goes without moving anything
		if (tocToggle && titleButtons) {
			classList.toggle(
				TOC_CLASS,
				titleButtons.getBoundingClientRect().bottom <= height,
			);
		}
		classList.add(SHOWN_CLASS);
	}

	/**
	 * @return {boolean} The reader is using the bar, a menu or the drawer
	 */
	function isBusy() {
		return (
			(menuCheckbox instanceof HTMLInputElement && menuCheckbox.checked) ||
			(tocCheckbox instanceof HTMLInputElement && tocCheckbox.checked) ||
			(!!search && search.contains(document.activeElement))
		);
	}

	/**
	 * @return {void}
	 */
	function update() {
		frame = 0;
		const y = window.scrollY;
		const delta = y - lastY;
		lastY = y;
		if (paused || toColumn.matches || isBusy()) {
			upDistance = 0;
			return;
		}
		const shown = classList.contains(SHOWN_CLASS);
		if (jumped || delta > 0) {
			jumped = false;
			upDistance = 0;
			if (y >= height) {
				classList.remove(SHOWN_CLASS);
				return;
			}
		}
		// Where the bar's own place is in view, it stays there
		if (y <= 0 || (y < height && !shown)) {
			unpin();
			upDistance = 0;
		} else if (delta < 0) {
			upDistance -= delta;
			if (!shown && upDistance >= height) {
				show();
			}
		}
	}

	window.addEventListener(
		"scroll",
		() => {
			if (!frame) {
				frame = window.requestAnimationFrame(update);
			}
		},
		{ passive: true },
	);
	toColumn.addEventListener("change", unpin);

	document.addEventListener("click", (event) => {
		const target = event.target;
		if (
			!event.defaultPrevented &&
			target instanceof Element &&
			target.closest('a[href^="#"]')
		) {
			jumped = true;
			window.setTimeout(() => {
				jumped = false;
			}, JUMP_MS);
		}
	});

	// Tabbing back into the bar brings it into view
	navBar.addEventListener("focusin", () => {
		if (
			window.scrollY >= height &&
			!toColumn.matches &&
			!classList.contains(SHOWN_CLASS)
		) {
			show();
		}
	});

	if (tocToggle && tocCheckbox instanceof HTMLInputElement) {
		tocToggle.addEventListener("click", () => {
			tocCheckbox.checked = !tocCheckbox.checked;
			tocCheckbox.dispatchEvent(new Event("input"));
		});
	}

	// VisualEditor's toolbar takes the top of the screen while it is open
	mw.hook("ve.activationStart").add(() => {
		paused = true;
		unpin();
	});
	mw.hook("ve.deactivationComplete").add(() => {
		paused = false;
		lastY = window.scrollY;
	});
}

module.exports = Object.freeze({ init });
