(function () {
  'use strict';

  /**
   * @class ShareDialog
   */
  mw.fw.ShareDialog = function MwFwShareDialog(config) {
    // Configuration initialization
    config = config || {};

    this.useAddThis = config.useAddThis;
    this.addThisToolId = config.addThisToolId;
    this.firebaseKey = config.firebaseKey;
    this.facebookAppId = config.facebookAppId;

    // Parent constructor
    mw.fw.ShareDialog.super.call(this, config);
  };

  // Initialization
  OO.inheritClass(mw.fw.ShareDialog, OO.ui.MessageDialog);

  mw.fw.ShareDialog.static.name = 'ShareDialog';

  mw.fw.ShareDialog.prototype.initialize = function () {
    mw.fw.ShareDialog.super.prototype.initialize.call(this);

    // Make UI elements
    this.content = new OO.ui.PanelLayout({
      padded: true,
      expanded: false,
    });
    this.$element.addClass('mw-fw-ui-shareDialog');

    // Make SNS Buttons
    if (this.useAddThis) {
      // AddThis
      this.$addThis = document.createElement('div');
      this.$addThis.classList.add('addthis_inline_share_toolbox');
      if (this.addThisToolId) {
        this.$addThis.classList.add(
          'addthis_inline_share_toolbox' + '_' + this.addThisToolId
        );
      }
      addthis.layers.refresh();
    } else {
      var items = [];
      if (this.facebookAppId) {
        this.facebookButton = new OO.ui.ButtonWidget({
          framed: false,
          icon: 'newWindow',
          label: mw.msg('skin-femiwiki-share-facebook'),
        });

        items.push(this.facebookButton);

        // Connect onClick function
        this.facebookButton.connect(this, {
          click: 'onFacebookButtonClick',
        });
        this.facebookButton.$element.addClass('mw-fw-ui-facebookButton');
      }
      this.twitterButton = new OO.ui.ButtonWidget({
        framed: false,
        icon: 'newWindow',
        label: mw.msg('skin-femiwiki-share-twitter'),
      });
      items.push(this.twitterButton);
      this.twitterButton.$element.addClass('mw-fw-ui-twitterButton');
      this.mediaButtonGroup = new OO.ui.ButtonGroupWidget({
        items: items,
      });
    }

    // Create a TextForm to copy
    this.urlWidget = new OO.ui.TextInputWidget({
      focusable: true,
      readOnly: true,
    });

    // Connect onClick function
    var urlWidget = this.urlWidget;
    this.urlWidget.$element.on('click', function () {
      urlWidget.select();
    });

    // Append elements
    if (this.useAddThis) {
      this.content.$element.append(this.$addThis);
    } else {
      this.content.$element.append(this.mediaButtonGroup.$element);
    }
    this.content.$element.append(this.urlWidget.$element);
    this.$body.append(this.content.$element);
  };

  mw.fw.ShareDialog.prototype.getSetupProcess = function (data) {
    data = data || {};
    var shareDialog = this;

    return mw.fw.ShareDialog.super.prototype.getSetupProcess
      .call(this, data)
      .next(function () {
        if (shareDialog.longUrl != data.url) {
          shareDialog.longUrl = data.url;
          shareDialog.shortUrl = undefined;
          shareDialog.updateUrl(shareDialog.longUrl);
          shareDialog.createShortUrl();
        }
      }, this);
  };

  mw.fw.ShareDialog.prototype.getReadyProcess = function (data) {
    data = data || {};
    return mw.fw.ShareDialog.super.prototype.getReadyProcess
      .call(this, data)
      .next(function () {
        this.urlWidget.select();
      }, this);
  };

  mw.fw.ShareDialog.prototype.onFacebookButtonClick = function () {
    FB.ui(
      {
        method: 'share',
        href: this.longUrl,
      },
      function (response) {}
    );
  };

  mw.fw.ShareDialog.prototype.updateUrl = function (url) {
    this.urlWidget.setValue(url);

    if (this.useAddThis) {
      addthis.layers.refresh(url);
    } else {
      var tweet =
        mw.config.get('wgPageName').replace(/_/g, ' ') +
        ' ' +
        url +
        ' #' +
        mw.config.get('wgSiteName');

      this.twitterButton.setHref(
        'https://twitter.com/intent/tweet?text=' + encodeURIComponent(tweet)
      );
    }
  };

  mw.fw.ShareDialog.prototype.createShortUrl = function (url) {
    if (!this.firebaseKey) {
      return;
    }
    var shareDialog = this;

    var xhr = new XMLHttpRequest();
    xhr.open(
      'POST',
      'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' +
        this.firebaseKey,
      true
    );

    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
      if (
        this.readyState === XMLHttpRequest.DONE &&
        this.status === 200 &&
        // Ensure that the url has not changed during processing, is it working?
        window.location.href === shareDialog.longUrl
      ) {
        var response = JSON.parse(this.responseText);
        shareDialog.shortUrl = response.shortLink;
        shareDialog.updateUrl(shareDialog.shortUrl);
        shareDialog.urlWidget.select();
      }
    };

    xhr.send(
      JSON.stringify({
        // Reference: https://firebase.google.com/docs/reference/dynamic-links/link-shortener
        dynamicLinkInfo: {
          dynamicLinkDomain: 'fmwk.page.link',
          link: this.longUrl,
          analyticsInfo: {
            googlePlayAnalytics: {
              utmCampaign: 'share',
            },
          },
        },
        suffix: {
          option: 'SHORT',
        },
      })
    );
  };

  mw.fw.ShareDialog.prototype.getBodyHeight = function () {
    return this.content.$element.outerHeight(true);
  };

  mw.fw.ShareDialog.prototype.getBodyWidth = function () {
    return this.content.$element.outerWidth(true);
  };
})();
