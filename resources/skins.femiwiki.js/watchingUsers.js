/** @type {HTMLElement|null} */ let watchLink = null;
/** @type {HTMLElement|null} */ let watchAnchor = null;
let watched = false;
let watchers = 0;

/**
 * @param {number} num
 * @returns void
 */
function updateWatcher(num) {
	if (!watchLink || !watchAnchor || num < 0) {
		return;
	}
	watchLink.classList.add("label");
	watchAnchor.textContent = num.toString();
}

/**
 * @param {boolean} newWatched
 */
function onWatchlistChange(newWatched) {
	// Finishing visual editing fires this again with the same status, which would
	// miscount the watchers, so compare it with the previous one.
	if (newWatched === watched) {
		return;
	}
	watched = newWatched;
	if (watched) {
		watchers++;
	} else {
		watchers--;
	}
}

/**
 * Core updates the label of watchAnchor to '(un)Watching...' and '(un)Watch', and we don't want it.
 * @returns void
 */
function onLabelChange() {
	if (!watchAnchor) {
		return;
	}
	switch (watchAnchor.textContent) {
		case mw.msg("watching"):
		case mw.msg("watch"):
		case mw.msg("unwatching"):
		case mw.msg("unwatch"):
			updateWatcher(watchers);
			break;
	}
}

/**
 * @param {*} data
 * @returns void
 */
function onApiDone(data) {
	if (!watchLink || !watchAnchor) {
		return;
	}

	/**@type {number}*/
	const newWatchers = Object.values(data.query.pages)[0].watchers;
	if (!newWatchers || newWatchers === watchers) {
		return;
	}

	watchers = newWatchers;
	updateWatcher(watchers);
	watchLink.classList.add("label");
}

/**
 * @returns void
 */
function init() {
	watched = document.getElementById("ca-watch") === null;
	watchLink = document.querySelector(".mw-watchlink, .mw-watchlink-watch");
	watchAnchor = watchLink && watchLink.querySelector(":scope > a");
	if (!watchLink || !watchAnchor) {
		return;
	}

	mw.hook("wikipage.watchlistChange").add(onWatchlistChange);
	new MutationObserver(onLabelChange).observe(watchAnchor, {
		childList: true,
		characterData: true,
		subtree: true,
	});

	new mw.Api()
		.get({
			action: "query",
			format: "json",
			titles: mw.config.get("wgPageName"),
			prop: "info",
			inprop: "watchers",
		})
		.done(onApiDone);
}

module.exports = Object.freeze({ init });
