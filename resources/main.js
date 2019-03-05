var _FW = {
  BBS_NS: [3902, 3904]
};

$(function() {
  function menuResize(divId) {
    var containerWidth =
        parseFloat($("#" + divId).css("width")) -
        parseFloat($("#" + divId).css("padding-left")) -
        parseFloat($("#" + divId).css("padding-right")),
      itemPadding =
        parseFloat($("#" + divId + " > div").css("padding-left")) +
        parseFloat($("#" + divId + " > div").css("padding-right")),
      itemMargin =
        parseFloat($("#" + divId + " > div").css("margin-left")) +
        parseFloat($("#" + divId + " > div").css("margin-right")),
      itemActualMinWidth =
        parseFloat($("#" + divId + " > div").css("min-width")) +
        itemPadding +
        itemMargin,
      items = $("#" + divId + " > div").filter(function() {
        return $(this).css("display") !== "none";
      }),
      itemLength = items.length;

    // Fit width of elements to parent
    var horizontalCapacity = Math.min(
      Math.floor(containerWidth / itemActualMinWidth),
      itemLength
    );
    if (
      itemLength % 2 == 0 &&
      horizontalCapacity > 1 &&
      horizontalCapacity % 2 != 0
    ) {
      // Always place the same number of elements in all rows.
      horizontalCapacity -= 1;
    }
    $("#" + divId + " > div").css(
      "width",
      Math.floor(containerWidth / horizontalCapacity - itemPadding - itemMargin)
    );

    // Let elements in each row has the same height.
    if (horizontalCapacity > 1) {
      for (var i = 0; i < itemLength; i += horizontalCapacity) {
        var maxHeight = 0;
        for (var j = 0; j < horizontalCapacity; j++) {
          var height = parseFloat(items.eq(i + j).css("height"));

          if (height > maxHeight) {
            maxHeight = height;
          }
        }
        for (var j = 0; j < horizontalCapacity; j++) {
          items.eq(i + j).css("height", maxHeight);
        }
      }
    } else {
      items.css("height", "auto");
    }
  }

  var searchInput = $("#searchInput"),
    searchClearButton = $("#searchClearButton");
  searchInput.on("input", function() {
    searchClearButton.toggle(!!this.value);
  });
  searchClearButton.click(function() {
    searchInput
      .val("")
      .trigger("input")
      .focus();
  });

  $("#fw-menu-toggle").click(function() {
    $("#fw-menu").toggle();
    menuResize("fw-menu");
    $("#fw-menu-toggle .badge").removeClass("active");
  });
  $("#p-menu-toggle a").click(function(e) {
    e.preventDefault();
    $("#p-actions-and-toolbox").toggle();
    menuResize("p-actions-and-toolbox");
  });
  $(window).resize(function() {
    menuResize("fw-menu");
    menuResize("p-actions-and-toolbox");
  });

  // Notification badge
  var alerts = +$("#pt-notifications-alert a").attr("data-counter-num");
  var notice = +$("#pt-notifications-notice a").attr("data-counter-num");
  var badge = alerts + notice;
  if (!isNaN(badge) && badge !== 0) {
    $("#fw-menu-toggle .badge")
      .addClass("active")
      .text(badge > 10 ? "+9" : badge);
  }

  // Set Mathjax linebreaks configuration
  if (typeof MathJax !== "undefined") {
    MathJax.Hub.Config({
      CommonHTML: { linebreaks: { automatic: true } },
      "HTML-CSS": { linebreaks: { automatic: true } },
      SVG: { linebreaks: { automatic: true } }
    });

    // Center single Mathjax line
    MathJax.Hub.Queue(function() {
      $(
        "#content p > span:only-child > span.MathJax, " +
          "#content p > span.mathjax-wrapper:only-child > div"
      ).each(function() {
        if (
          !$(this)
            .parent()
            .parent()
            .clone()
            .children()
            .remove()
            .end()
            .text()
            .trim().length
        ) {
          $(this)
            .parent()
            .css("display", "block");
          $(this)
            .parent()
            .css("text-align", "center");
        }
      });
    });
  }
});
