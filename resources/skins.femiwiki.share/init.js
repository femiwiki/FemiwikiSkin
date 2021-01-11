(function () {
  'use strict';

  function init() {
    OO.ui.infuse($('#p-share')).on('click', function () {
      var addThisPubId, firebaseKey, windowManager, facebookAppId, shareDialog;
      firebaseKey = firebaseKey || mw.config.get('wgFemiwikiFirebaseKey');
      addThisPubId = addThisPubId || mw.config.get('wgFemiwikiAddThisPubId');
      if (addThisPubId) {
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
      } else {
        facebookAppId =
          facebookAppId || mw.config.get('wgFemiwikiFacebookAppId');
      }

      mw.loader.using(['skins.femiwiki.share.ui']).done(function () {
        windowManager = windowManager || OO.ui.getWindowManager();
        if (shareDialog === undefined) {
          shareDialog = new mw.fw.ShareDialog({
            addThisPubId: addThisPubId,
            firebaseKey: firebaseKey,
            facebookAppId: facebookAppId,
          });
          windowManager.addWindows([shareDialog]);
        }

        if (!addThisPubId && facebookAppId) {
          // Initialize Facebook SDK
          window.fbAsyncInit = function () {
            FB.init({
              appId: facebookAppId,
              autoLogAppEvents: true,
              xfbml: true,
              version: 'v4.0',
            });
          };
          // below snippet is from https://developers.facebook.com/docs/php/howto/example_access_token_from_javascript
          (function (d, s, id) {
            var js,
              fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
              return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk.js';
            fjs.parentNode.insertBefore(js, fjs);
          })(document, 'script', 'facebook-jssdk');
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
