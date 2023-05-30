(function () {
  'use strict';

  function init() {
    OO.ui.infuse($('#p-share')).on('click', function () {
      if (navigator.share) {
        navigator.share({
          url: window.location.href,
          title: mw.config.get('wgPageName').replace(/_/g, ' '),
        });
      } else {
        var windowManager, shareDialog;
        var firebaseKey = firebaseKey || mw.config.get('wgFemiwikiFirebaseKey');

        mw.loader.using(['skins.femiwiki.share.ui']).done(function () {
          windowManager = windowManager || OO.ui.getWindowManager();
          if (shareDialog === undefined) {
            shareDialog = new mw.fw.ShareDialog({
              firebaseKey: firebaseKey,
            });
            windowManager.addWindows([shareDialog]);
          }

          windowManager.openWindow(shareDialog, {
            url: window.location.href,
            actions: [
              {
                action: 'accept',
                label: mw.msg('skin-femiwiki-share-dismiss'),
                flags: 'primary',
              },
            ],
          });
        });
      }
    });
  }

  $(init);
})();
