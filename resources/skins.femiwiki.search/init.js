var Vue = require(/** @type {string} */ ("vue"));
var typeaheadSearch = require(
	/** @type {string} */ ("mediawiki.skinning.typeaheadSearch"),
);

/**
 * Replaces the server-rendered search box with Codex TypeaheadSearch, as Vector 2022 does.
 *
 * @return {void}
 */
function init() {
	var searchBox = document.querySelector(".fw-search-box");
	if (!searchBox) {
		return;
	}
	var searchForm = searchBox.querySelector(".cdx-search-input");
	var titleInput = /** @type {HTMLInputElement|null} */ (
		searchBox.querySelector("input[name=title]")
	);
	var search = /** @type {HTMLInputElement|null} */ (
		searchBox.querySelector("input[name=search]")
	);
	if (!searchForm || !titleInput || !search) {
		return;
	}

	var urlGenerator = typeaheadSearch.urlGenerator(mw.config.get("wgScript"));
	var app = Vue.createMwApp(typeaheadSearch.App, {
		prefixClass: "fw-",
		id: searchForm.id,
		autocapitalizeValue: search.getAttribute("autocapitalize"),
		autofocusInput: search === document.activeElement,
		action: searchForm.getAttribute("action"),
		searchAccessKey: search.getAttribute("accessKey"),
		searchPageTitle: titleInput.value,
		restClient: typeaheadSearch.restSearchClient(
			`${mw.config.get("wgScriptPath")}/rest.php`,
			urlGenerator,
		),
		urlGenerator: urlGenerator,
		searchTitle: search.getAttribute("title"),
		searchPlaceholder: search.getAttribute("placeholder"),
		searchQuery: search.value,
		// The top bar has no room for a button; Enter submits.
		searchButtonLabel: "",
		showThumbnail: true,
		// Shown only for pages that have one.
		showDescription: true,
		highlightQuery: true,
	}).mount(searchBox.querySelector(".fw-typeahead-search-container"));
	searchWhileComposing(app.searchForm);
}

/**
 * Searches while a Korean (or other IME) syllable is still being composed.
 *
 * Vue's v-model skips input events during IME composition, and Codex reads the value on
 * compositionupdate, which fires before the input value changes. So the search ran one
 * keystroke behind and the menu stayed closed until the word was finished with a space.
 *
 * @param {any} typeahead The mounted CdxTypeaheadSearch component
 * @return {void}
 */
function searchWhileComposing(typeahead) {
	var input = /** @type {HTMLInputElement|null} */ (
		typeahead && typeahead.$el.querySelector("input[name=search]")
	);
	if (!input) {
		return;
	}
	input.addEventListener("input", (event) => {
		if (!(/** @type {InputEvent} */ (event).isComposing)) {
			return;
		}
		var value = /** @type {HTMLInputElement} */ (event.target).value;
		typeahead.inputValue = value;
		typeahead.onUpdateInputValue(value);
	});
}

module.exports = { init: init };
