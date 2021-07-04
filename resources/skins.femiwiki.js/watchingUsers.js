/** @type JQuery<HTMLElement>?*/ var $watchLink;
/** @type JQuery<HTMLElement>?*/ var $watchAnchor;
var watched = false;
var watchers = 0;

/**
 * @param {number} num
 * @returns void
 */
function updateWatcher(num) {
  if (!$watchLink || !$watchAnchor || num < 0) {
    return;
  }
  $watchLink.addClass('label');
  $watchAnchor.html(num.toString());
}

/**
 *
 * @param {*} _
 * @param {string} otherAction
 */
function onWatchpage(_, otherAction) {
  // We have to check the previous watching status as finishing visual editing triggers
  // 'watchpage.mw' event again even if the watching status is not changed. It could make a false
  // increasement or decreasement of the watching counter.
  var newWatched = otherAction == 'watch';
  if (newWatched == watched) {
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
 * $watchAnchor updates the label to '(un)Watching...' and '(un)Watch', and we don't want it.
 * @param {*} handler
 * @returns
 */
function onDOMSubtreeModified(handler) {
  switch (handler.target.innerHTML) {
    case mw.msg('watching'):
    case mw.msg('watch'):
    case mw.msg('unwatching'):
    case mw.msg('unwatch'):
      updateWatcher(watchers);
      break;
  }
}

/**
 * @param {*} data
 * @returns void
 */
function onApiDone(data) {
  if (!$watchLink || !$watchAnchor) {
    return;
  }

  /**@type {number}*/
  var newWatchers = Object.values(data.query.pages)[0].watchers;
  if (!newWatchers || newWatchers === watchers) {
    return;
  }

  watchers = newWatchers;
  updateWatcher(watchers);
  $watchLink.addClass('label');
}

/**
 * @returns void
 */
function init() {
  watched = $('#ca-watch').length == 0;
  $watchLink = $('.mw-watchlink, .mw-watchlink-watch');
  if (!$watchLink) {
    return;
  }
  $watchAnchor = $watchLink.children('a');

  $watchLink.on('watchpage.mw', onWatchpage);
  $watchLink.on('DOMSubtreeModified', onDOMSubtreeModified);

  new mw.Api()
    .get({
      action: 'query',
      format: 'json',
      titles: mw.config.get('wgPageName'),
      prop: 'info',
      inprop: 'watchers',
    })
    .done(onApiDone);
}

module.exports = Object.freeze({ init: init });
