/** @type {{useDiscussionTools: boolean, useFlow: boolean, openTopicDays: number}} */
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
function showFlowStatus(talkName) {
	new mw.Api()
		.get({
			action: "flow",
			page: talkName,
			submodule: "view-topiclist",
			vtllimit: 10,
		})
		.done((data) => {
			const topiclist = data.flow["view-topiclist"].result.topiclist;
			const revisions = topiclist.revisions;

			let locked = 0;
			for (const key in revisions) {
				if (revisions[key].changeType === "lock-topic") locked++;
			}

			// Only the latest 10 topics are fetched
			const num = topiclist.roots.length;
			showStatus(num > 9 ? "9+" : num, num - locked > 0);
		});
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
	if (!config.useDiscussionTools && !config.useFlow) {
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
			if (config.useFlow && contentModel === "flow-board") {
				showFlowStatus(talkName);
			} else if (config.useDiscussionTools && contentModel === "wikitext") {
				showWikitextStatus(talkName);
			}
		});
}

module.exports = Object.freeze({ init });
