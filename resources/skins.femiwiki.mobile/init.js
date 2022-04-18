// Note: A preference of MediaWiki is stored as string always.
var newWikitext = mw.user.options.get('visualeditor-newwikitext') === '1';

/**
 * @return {void}
 */
function click(/**@type Event*/ ev) {
  if (ev === null || ev.currentTarget === null) {
    return;
  }
  var anchor = /** @type {HTMLAnchorElement} */ (ev.currentTarget);
  var url = new mw.Uri(anchor.href);
  var visualEditing =
    // VisualEdit
    url.query.veaction === 'edit' ||
    // If the new wikitext mode is enabled, all cases of edit will be done on VisualEditor
    newWikitext;

  if (!visualEditing) {
    // Do nothing here to allow the user to move to href.
    return;
  }
  ev.preventDefault();
  var switchUri = url.extend({
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
      '#ca-ve-edit a, #ca-edit a, .mw-editsection a, .edit-link'
    );

  for (var i = 0; i < $allEditLinks.length; i++) {
    $allEditLinks[i].addEventListener('click', click);
  }
}

main();
