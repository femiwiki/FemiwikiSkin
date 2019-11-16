(function() {
  "use strict";

  function init() {
    var facebookAppId, firebaseKey, windowManager, shareDialog;

    OO.ui.infuse($("#p-share")).on("click", function() {
      mw.loader.using(["skins.femiwiki.share.ui"]).done(function() {
        facebookAppId =
          facebookAppId || mw.config.get("wgFemiwikiFacebookAppId");
        firebaseKey = firebaseKey || mw.config.get("wgFemiwikiFirebaseKey");
        windowManager = windowManager || OO.ui.getWindowManager();
        if (shareDialog === undefined) {
          shareDialog = new mw.fw.ShareDialog({
            facebookAppId: facebookAppId,
            firebaseKey: firebaseKey
          });
          windowManager.addWindows([shareDialog]);
        }

        if (facebookAppId) {
          // Initialize Facebook SDK
          window.fbAsyncInit = function() {
            FB.init({
              appId: facebookAppId,
              autoLogAppEvents: true,
              xfbml: true,
              version: "v4.0"
            });
          };
          // below snippet is from https://developers.facebook.com/docs/php/howto/example_access_token_from_javascript
          (function(d, s, id) {
            var js,
              fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
              return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
          })(document, "script", "facebook-jssdk");
        }

        windowManager.openWindow(shareDialog, {
          url: window.location.href,
          actions: [
            {
              action: "accept",
              label: mw.msg("skin-femiwiki-share-dismiss"),
              flags: "primary"
            }
          ]
        });
      });
    });
  }

  $(init);
})();
