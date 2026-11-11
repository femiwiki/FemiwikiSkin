/** @type {{useDiscussionTools: boolean, openTopicDays: number}} */
// @ts-expect-error Virtual file from skin.json
const config = require("./config.json");

/**
 * @param {number|string} num
 * @param {boolean} open
 */
function showStatus(num, open) {
	if (num === 0) {
		return;
	}
	const talk = document.getElementById("ca-talk");
	if (!talk) {
		return;
	}
	talk.querySelectorAll("a").forEach((a) => {
		a.textContent = String(num);
	});
	talk.classList.add("label");
	if (open) {
		talk.classList.add("has-open-topic");
	}
}

/**
 * @param {string} talkName
 */
function showWikitextStatus(talkName) {
	new mw.Api()
		.get({
			action: "discussiontoolspageinfo",
			page: talkName,
			prop: "threaditemshtml",
			threaditemsflags: "noreplies",
			formatversion: 2,
		})
		.done((data) => {
			const topics = data.discussiontoolspageinfo.threaditemshtml.filter(
				/** @param {{headingLevel: number|null}} item */
				// The placeholder for content above the first heading has no level
				(item) => item.headingLevel !== null,
			);

			const openSince = Date.now() - config.openTopicDays * 24 * 60 * 60 * 1000;
			const open = topics.some(
				/** @param {{latestReplyTimestamp: string|null}} topic */
				(topic) =>
					topic.latestReplyTimestamp &&
					Date.parse(topic.latestReplyTimestamp) > openSince,
			);

			showStatus(topics.length, open);
		});
}

function init() {
	const title = new mw.Title(mw.config.get("wgPageName"));
	const talkPage = title.getTalkPage();
	const talk = document.getElementById("ca-talk");
	if (!talkPage || !talk || title === talkPage) {
		return;
	}
	if (talk.classList.contains("new")) {
		return;
	}
	if (!config.useDiscussionTools) {
		return;
	}
	const talkName = talkPage.getPrefixedText();
	new mw.Api()
		.get({
			action: "query",
			prop: "info",
			titles: talkName,
			formatversion: 2,
		})
		.done((data) => {
			const contentModel = data.query.pages[0].contentmodel;
			if (contentModel === "wikitext") {
				showWikitextStatus(talkName);
			}
		});
}

module.exports = Object.freeze({ init });
