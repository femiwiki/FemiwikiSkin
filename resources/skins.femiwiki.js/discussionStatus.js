/** @type {{useDiscussionTools: boolean, useFlow: boolean, openTopicDays: number}} */
// @ts-expect-error Virtual file from skin.json
var config = require("./config.json");

/**
 * @param {number|string} num
 * @param {boolean} open
 */
function showStatus(num, open) {
	if (num === 0) {
		return;
	}
	var $talk = $("#ca-talk");
	$talk.find("a").text(num);
	$talk.addClass("label");
	if (open) {
		$talk.addClass("has-open-topic");
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
			var topiclist = data.flow["view-topiclist"].result.topiclist;
			var revisions = topiclist.revisions;

			var locked = 0;
			for (var key in revisions) {
				if (revisions[key].changeType === "lock-topic") locked++;
			}

			// Only the latest 10 topics are fetched
			var num = topiclist.roots.length;
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
			var topics = data.discussiontoolspageinfo.threaditemshtml.filter(
				/** @param {{headingLevel: number|null}} item */
				(item) => {
					// The placeholder for content above the first heading has no level
					return item.headingLevel !== null;
				},
			);

			var openSince = Date.now() - config.openTopicDays * 24 * 60 * 60 * 1000;
			var open = topics.some(
				/** @param {{latestReplyTimestamp: string|null}} topic */
				(topic) =>
					topic.latestReplyTimestamp &&
					Date.parse(topic.latestReplyTimestamp) > openSince,
			);

			showStatus(topics.length, open);
		});
}

function init() {
	var title = new mw.Title(mw.config.get("wgPageName"));
	var talkPage = title.getTalkPage();
	var $talk = $("#ca-talk");
	if (!talkPage || $talk.length === 0 || title === talkPage) {
		return;
	}
	if ($talk.hasClass("new")) {
		return;
	}
	if (!config.useDiscussionTools && !config.useFlow) {
		return;
	}
	var talkName = talkPage.getPrefixedText();
	new mw.Api()
		.get({
			action: "query",
			prop: "info",
			titles: talkName,
			formatversion: 2,
		})
		.done((data) => {
			var contentModel = data.query.pages[0].contentmodel;
			if (config.useFlow && contentModel === "flow-board") {
				showFlowStatus(talkName);
			} else if (config.useDiscussionTools && contentModel === "wikitext") {
				showWikitextStatus(talkName);
			}
		});
}

module.exports = Object.freeze({ init: init });
