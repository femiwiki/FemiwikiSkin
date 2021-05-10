var newWikitext = mw.user.options.get('visualeditor-newwikitext') === 1;

/**
 * @return {void}
 */
function click(/**@type Event*/ e) {
  // @ts-ignore
  var href = this.getAttribute('href') || e.target.toString();
  var veaction = /\bveaction\b/.test(href);
  var supported = !veaction && !newWikitext;

  if (supported) {
    // Do noting here to allow the user to move to href.
    return;
  }
  e.preventDefault();
  var switchUri = new mw.Uri(href).extend({
    mobileaction: 'toggle_view_desktop',
  });

  mw.loader.using(['oojs', 'oojs-ui-windows'], function () {
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
