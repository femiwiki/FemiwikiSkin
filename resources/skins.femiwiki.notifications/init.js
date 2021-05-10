(function () {
  'use strict';

  function init() {
    var myWidget;
    /** @type {EchoApi} */ var echoApi;
    var $existingLink = $('#pt-notifications-all a');
    var num = $existingLink.attr('data-counter-num');
    var badgeLabel = $existingLink.attr('data-counter-text');
    var hasUnseen = $existingLink.hasClass('mw-echo-unseen-notifications');
    var links = {
      notifications:
        $('#pt-notifications-all a').attr('href') ||
        mw.util.getUrl('Special:Notifications'),
      preferences:
        ($('#pt-preferences a').attr('href') ||
          mw.util.getUrl('Special:Preferences')) + '#mw-prefsection-echo',
    };

    // Respond to click on the notification button and load the UI on demand
    $('.mw-echo-notification-badge-fw-nojs').on('click', function (e) {
      var time = mw.now();

      if (e.which !== 1 || $(this).data('clicked')) {
        return false;
      }

      $(this).data('clicked', true);

      // Dim the button while we load
      $(this).addClass('mw-echo-notifications-badge-dimmed');

      // Fire the notification API requests
      echoApi = new mw.echo.api.EchoApi();
      echoApi.fetchNotifications('all').then(function (data) {
        mw.track('timing.MediaWiki.echo.overlay.api', mw.now() - time);
        return data;
      });

      // Load the ui
      mw.loader.using('ext.echo.ui.desktop', function () {
        /** @type {Controller} */ var controller;
        /** @type {ModelManager} */ var modelManager;
        /** @type {UnreadNotificationCounter} */ var unreadCounter;
        var maxNotificationCount = mw.config.get('wgEchoMaxNotificationCount');

        // Overlay
        $('body').append(mw.echo.ui.$overlay);

        unreadCounter = new mw.echo.dm.UnreadNotificationCounter(
          echoApi,
          'all',
          maxNotificationCount
        );
        modelManager = new mw.echo.dm.ModelManager(unreadCounter, {
          type: 'all',
        });
        controller = new mw.echo.Controller(echoApi, modelManager);

        // workaround https://github.com/femiwiki/FemiwikiSkin/issues/212
        mw.echo.ui.NotificationBadgeWidget.prototype.markAllReadButtonWorkaround =
          function () {
            echoApi.markAllRead(
              modelManager
                .getFiltersModel()
                .getSourcePagesModel()
                .getCurrentSource(),
              ['alert', 'message']
            );
          };
        mw.echo.ui.widget = new mw.echo.ui.NotificationBadgeWidget(
          controller,
          modelManager,
          links,
          {
            numItems: Number(num),
            convertedNumber: badgeLabel,
            hasUnseen: hasUnseen,
            badgeIcon: 'bell',
            $overlay: mw.echo.ui.$overlay,
            href: $existingLink.attr('href'),
          }
        );

        modelManager.on('allTalkRead', function () {
          // If there was a talk page notification, get rid of it
          $('#pt-mytalk a').removeClass('mw-echo-alert').text(mw.msg('mytalk'));
        });

        mw.echo.ui.widget.markAllReadButton.connect(mw.echo.ui.widget, {
          click: 'markAllReadButtonWorkaround',
        });

        // Replace the link button with the ooui button
        $existingLink.parent().replaceWith(mw.echo.ui.widget.$element);

        // HACK: Now that the module loaded, show the popup
        myWidget = mw.echo.ui.widget;
        myWidget.once('finishLoading', function () {
          // Log timing after notifications are shown
          mw.track('timing.MediaWiki.echo.overlay', mw.now() - time);
        });
        myWidget.popup.toggle(true);
        mw.track('timing.MediaWiki.echo.overlay.ooui', mw.now() - time);
      });

      if (hasUnseen) {
        // Clicked on the flyout due to having unread notifications
        mw.track('counter.MediaWiki.echo.unseen.click');
      }

      // console.log('finally');
      // Prevent default
      return false;
    });
  }

  // Early execute of init
  if (
    document.readyState === 'interactive' ||
    document.readyState === 'complete'
  ) {
    init();
  } else {
    $(init);
  }
})();
