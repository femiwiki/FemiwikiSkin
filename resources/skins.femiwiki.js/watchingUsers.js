function init() {
  var $watchLink = document.querySelector('.mw-watchlink');
  var $watchAnchor = document.querySelector('.mw-watchlink a');
  if (!$watchLink || mw.config.get('wgArticleId') === 0) {
    return;
  }
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
      var watchers;
      for (var p in data.query.pages) {
        watchers = pages[p].watchers;
      }
      if (!$watchLink || !watchers || watchers === 0) {
        return;
      }

      $watchAnchor.innerHTML = watchers;
      $watchLink.classList.add('label');
    });
}

module.exports = Object.freeze({ init: init });
