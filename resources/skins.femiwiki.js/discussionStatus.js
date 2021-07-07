function init() {
  var title = new mw.Title(mw.config.get('wgPageName'));
  var talkPage = title.getTalkPage();
  var $talk = $('#ca-talk');
  if (!talkPage || $talk.length === 0 || title == talkPage) {
    return;
  }
  var talkName = talkPage.getPrefixedText();
  var api = new mw.Api();

  api
    .get({
      action: 'flow',
      page: talkName,
      submodule: 'view-topiclist',
      vtllimit: 10,
    })
    .done(function (data) {
      var topiclist = data.flow['view-topiclist']['result']['topiclist'];
      var revisions = topiclist['revisions'];

      var locked = 0;
      for (var key in revisions) {
        if (revisions[key]['changeType'] == 'lock-topic') locked++;
      }

      var roots = topiclist['roots'];
      var num = roots.length;
      var $anchor = $('#ca-talk a');
      $anchor.text(num > 9 ? '9+' : num);
      $talk.addClass('label');
      var open = num - locked;
      if (open) {
        $talk.addClass('has-open-topic');
      }
    });
}

module.exports = Object.freeze({ init: init });
