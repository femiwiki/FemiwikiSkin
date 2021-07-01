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
      var pages = data.query.pages;
      var newWatchers;
      for (var p in data.query.pages) {
        newWatchers = pages[p].watchers;
      }
      if (!$watchLink || !newWatchers || newWatchers === 0) {
        return;
      }
      watchers = newWatchers;

      if ($watchAnchor) {
        $watchAnchor.html(watchers.toString());
      }
      $watchLink.addClass('label');
    });
}

module.exports = Object.freeze({ init: init });
