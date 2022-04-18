var newWikitext = mw.user.options.get('visualeditor-newwikitext') === 1;

/**
 * @return {void}
 */
function click(/**@type Event*/ e) {
  // @ts-ignore
  var href = e.currentTarget.href || e.currentTarget.getAttribute('href');
  var url = new mw.Uri(href);
  var visualEditing =
    // VisualEdit
    url.query.veaction === 'edit' ||
    // New wikitext mode edit in VE
    (newWikitext && url.query.veaction === 'editsource');

  if (!visualEditing) {
    // Do nothing here to allow the user to move to href.
    return;
  }
  e.preventDefault();
  var switchUri = new mw.Uri(href).extend({
    mobileaction: 'toggle_view_desktop',
  });

  mw.loader.using(['oojs', 'oojs-ui-windows'], function () {
    if (!OO.ui) {
      return;
    }
    OO.ui
      .confirm(mw.msg('skin-femiwiki-desktop-switch-confirm'))
      .done(function (confirmed) {
        if (confirmed) {
          window.location.href = switchUri.toString();
        } else {
          notifyUnsupported();
        }
      });
  });
}

/**
 * @return {void}
 */
function notifyUnsupported() {
  mw.notify(mw.msg('skin-femiwiki-desktop-switch-canceled'));
}

/**
 * @return {void}
 */
function main() {
  if (!mw.config.get('wgMFIsPageContentModelEditable')) {
    return;
  }
  if (
    mw.config.get('wgNamespaceNumber') ===
    mw.config.get('wgNamespaceIds')['special']
  ) {
    return;
  }

  /** @type NodeListOf<Element> */ var $allEditLinks =
    document.querySelectorAll(
      '#ca-ve-edit, #ca-edit, .mw-editsection a, .edit-link'
    );

  for (var i = 0; i < $allEditLinks.length; i++) {
    $allEditLinks[i].addEventListener('click', click);
  }
}

main();
