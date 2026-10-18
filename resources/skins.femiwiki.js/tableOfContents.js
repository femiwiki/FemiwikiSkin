var CURRENT_CLASS = "fw-toc-current";
// Vector's thresholds: a heading counts as reached within 75px of the top, checked at most every 200ms
var TOP_MARGIN = 75;
var THROTTLE_MS = 200;

/**
 * @param {() => void} callback
 * @param {number} frameCount
 * @return {void}
 */
function deferUntilFrame(callback, frameCount) {
	if (frameCount === 0) {
		callback();
		return;
	}
	window.requestAnimationFrame(() => {
		deferUntilFrame(callback, frameCount - 1);
	});
}

/**
 * Marks the section the reader is in, and closes the drawer once a section is picked.
 *
 * Follows Vector's sectionObserver.js: on scroll, an IntersectionObserver reports where the
 * headings are, so finding the current one needs no forced layout.
 *
 * @return {void}
 */
function init() {
	var toc = document.getElementById("fw-toc");
	var checkbox = document.getElementById("fw-toc-checkbox");
	var body = document.getElementById("bodyContent");
	if (!toc || !(checkbox instanceof HTMLInputElement) || !body) {
		return;
	}
	var tocCheckbox = checkbox;
	var panel = toc.querySelector(".fw-toc-panel");
	var top = toc.querySelector(".fw-toc-top");

	/** @type {Map<Element, Element>} */
	var items = new Map();
	if (top) {
		// The content's own top stands for the part above the first heading
		items.set(body, top);
	}
	var links = toc.querySelectorAll(".fw-toc-link[data-anchor]");
	for (let i = 0; i < links.length; i++) {
		const anchor = links[i].getAttribute("data-anchor");
		const heading = anchor && document.getElementById(anchor);
		if (heading && links[i].parentElement) {
			items.set(
				heading.closest(".mw-heading") || heading,
				/** @type {Element} */ (links[i].parentElement),
			);
		}
	}

	/** @type {Element|null} */
	var current = null;

	/**
	 * @param {Element|null|undefined} item
	 * @return {void}
	 */
	function setCurrent(item) {
		if (!item || item === current) {
			return;
		}
		if (current) {
			current.classList.remove(CURRENT_CLASS);
		}
		current = item;
		item.classList.add(CURRENT_CLASS);
		// Keep the mark in view in a ToC taller than the screen, without scrolling the page.
		if (panel && panel.scrollHeight > panel.clientHeight) {
			const link = item.querySelector(".fw-toc-link") || item;
			const linkRect = link.getBoundingClientRect();
			const panelRect = panel.getBoundingClientRect();
			if (linkRect.top < panelRect.top || linkRect.bottom > panelRect.bottom) {
				panel.scrollTop +=
					linkRect.top - panelRect.top - panel.clientHeight / 2;
			}
		}
	}

	// The current section is the last one whose heading has reached the top margin, or else the
	// nearest one below it. The observer is only asked for positions, so it disconnects each time.
	var observer = new IntersectionObserver((entries) => {
		/** @type {IntersectionObserverEntry|undefined} */
		var above;
		/** @type {IntersectionObserverEntry|undefined} */
		var below;
		entries.forEach((entry) => {
			var rect = entry.boundingClientRect;
			// A hidden heading has no box
			if (rect.top === 0 && rect.bottom === 0) {
				return;
			}
			if (rect.top - TOP_MARGIN <= 0) {
				if (!above || rect.top > above.boundingClientRect.top) {
					above = entry;
				}
			} else if (!below || rect.top < below.boundingClientRect.top) {
				below = entry;
			}
		});
		var closest = above || below;
		if (closest) {
			setCurrent(items.get(closest.target));
		}
		observer.disconnect();
	});

	/**
	 * @return {void}
	 */
	function calcIntersection() {
		items.forEach((_item, element) => {
			observer.observe(element);
		});
	}

	/** @type {number|undefined} */
	var timeoutId;
	var paused = false;

	/**
	 * @return {void}
	 */
	function handleScroll() {
		if (!paused && timeoutId === undefined) {
			timeoutId = window.setTimeout(() => {
				timeoutId = undefined;
				calcIntersection();
			}, THROTTLE_MS);
		}
	}

	toc.addEventListener("click", (event) => {
		var target = /** @type {Element} */ (event.target);
		var link = target.closest && target.closest(".fw-toc-link");
		if (!link) {
			return;
		}
		// Mark the pick even where the page cannot scroll it to the top, and ignore the jump to it.
		// Firefox can take three frames to paint the new scroll position, as Vector found.
		setCurrent(link.parentElement);
		paused = true;
		window.clearTimeout(timeoutId);
		timeoutId = undefined;
		deferUntilFrame(() => {
			paused = false;
		}, 3);
		if (tocCheckbox.checked) {
			tocCheckbox.checked = false;
			tocCheckbox.dispatchEvent(new Event("input"));
		}
	});

	window.addEventListener("scroll", handleScroll, { passive: true });
	window.addEventListener("resize", handleScroll);
	calcIntersection();
}

module.exports = Object.freeze({ init: init });
