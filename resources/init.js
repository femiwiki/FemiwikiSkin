$(function() {
  "use strict";

  var myWidget,
    echoApi,
    $existingLink = $("#pt-notifications-echo a"),
    num = $existingLink.attr("data-counter-num"),
    badgeLabel = $existingLink.attr("data-counter-text"),
    hasUnseen = $existingLink.hasClass("mw-echo-unseen-notifications"),
    links = {
      notifications:
        $("#pt-notifications-echo a").attr("href") ||
        mw.util.getUrl("Special:Notifications"),
      preferences:
        ($("#pt-preferences a").attr("href") ||
          mw.util.getUrl("Special:Preferences")) + "#mw-prefsection-echo"
    };

  // Respond to click on the notification button and load the UI on demand
  $(".mw-echo-notification-badge-fw-nojs").on("click", function(e) {
    var time = mw.now();

    if (e.which !== 1 || $(this).data("clicked")) {
      return false;
    }

    $(this).data("clicked", true);

    // Dim the button while we load
    $(this).addClass("mw-echo-notifications-badge-dimmed");

    // Fire the notification API requests
    echoApi = new mw.echo.api.EchoApi();
    echoApi.fetchNotifications("all").then(function(data) {
      mw.track("timing.MediaWiki.echo.overlay.api", mw.now() - time);
      return data;
    });

    // Load the ui
    mw.loader.using("ext.echo.ui.desktop", function() {
      var controller,
        modelManager,
        unreadCounter,
        maxNotificationCount = mw.config.get("wgEchoMaxNotificationCount");

      // Overlay
      $("body").append(mw.echo.ui.$overlay);

      unreadCounter = new mw.echo.dm.UnreadNotificationCounter(
        echoApi,
        "all",
        maxNotificationCount
      );
      modelManager = new mw.echo.dm.ModelManager(unreadCounter, {
        type: "all"
      });
      controller = new mw.echo.Controller(echoApi, modelManager);

      mw.echo.ui.widget = new mw.echo.ui.NotificationBadgeWidget(
        controller,
        modelManager,
        links,
        {
          numItems: Number(num),
          convertedNumber: badgeLabel,
          hasUnseen: hasUnseen,
          badgeIcon: "bell",
          $overlay: mw.echo.ui.$overlay,
          href: $existingLink.attr("href")
        }
      );

      modelManager.on("allTalkRead", function() {
        // If there was a talk page notification, get rid of it
        $("#pt-mytalk a")
          .removeClass("mw-echo-alert")
          .text(mw.msg("mytalk"));
      });

      // Replace the link button with the ooui button
      $existingLink.parent().replaceWith(mw.echo.ui.widget.$element);

      // HACK: Now that the module loaded, show the popup
      myWidget = mw.echo.ui.widget;
      myWidget.once("finishLoading", function() {
        // Log timing after notifications are shown
        mw.track("timing.MediaWiki.echo.overlay", mw.now() - time);
      });
      myWidget.popup.toggle(true);
      mw.track("timing.MediaWiki.echo.overlay.ooui", mw.now() - time);
    });

    if (hasUnseen) {
      // Clicked on the flyout due to having unread notifications
      mw.track("counter.MediaWiki.echo.unseen.click");
    }

    // Prevent default
    return false;
  });
});
