(function () {
  'use strict';

  function init() {
    OO.ui.infuse($('#p-share')).on('click', function () {
      var windowManager, shareDialog;
      var firebaseKey = firebaseKey || mw.config.get('wgFemiwikiFirebaseKey');
      var useAddThis = useAddThis || mw.config.get('wgFemiwikiUseAddThis');
      var addThisToolId =
        addThisToolId || mw.config.get('wgFemiwikiAddThisToolId');

      if (useAddThis) {
        addthis_config = addthis_config || {};
        addthis_config['services_exclude'] = 'print';
        addthis_config['ui_language'] = mw.config.get('wgUserLanguage');
        addthis_share = addthis_share || {};
        addthis_share = {
          passthrough: {
            twitter: {
              text: mw.config.get('wgPageName').replace(/_/g, ' '),
              hashtags: mw.config.get('wgSiteName'),
            },
          },
        };
      }

      mw.loader.using(['skins.femiwiki.share.ui']).done(function () {
        windowManager = windowManager || OO.ui.getWindowManager();
        if (shareDialog === undefined) {
          shareDialog = new mw.fw.ShareDialog({
            useAddThis: useAddThis,
            addThisToolId: addThisToolId,
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
    });
  }

  $(init);
})();
