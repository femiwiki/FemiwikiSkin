function init() {
  var $watchLink = $('.mw-watchlink, .mw-watchlink-watch');
  if (!$watchLink) {
    return;
  }
  var $watchAnchor = $watchLink.children('a');
  var watchers = 0;
  $watchLink.on('watchpage.mw', function (_, otherAction) {
    if (otherAction == 'watch') {
      watchers++;
    } else {
      watchers--;
    }
  });

  $watchLink.on('DOMSubtreeModified', function (event) {
    switch (event.target.innerHTML) {
      case mw.msg('watching'):
      case mw.msg('watch'):
        $watchLink.addClass('label');
      case mw.msg('unwatching'):
      case mw.msg('unwatch'):
        $watchAnchor.html(watchers.toString());
    }
  });
  new mw.Api()
    .get({
      action: 'query',
      format: 'json',
      titles: mw.config.get('wgPageName'),
      prop: 'info',
      inprop: 'watchers',
    })
    .done(function (data) {
      if (!$watchLink || !$watchAnchor) {
        return;
      }

      var newWatchers = Object.values(data.query.pages)[0].watchers;
      if (!newWatchers || newWatchers === watchers) {
        return;
      }

      watchers = newWatchers;
      $watchAnchor.html(watchers.toString());
      $watchLink.addClass('label');
    });
}

module.exports = Object.freeze({ init: init });
