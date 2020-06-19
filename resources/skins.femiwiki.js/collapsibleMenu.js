function init() {
  /**
   * @param {string} divId
   * @return {void}
   */
  function menuResize(divId) {
    var containerWidth =
        parseFloat($('#' + divId).css('width')) -
        parseFloat($('#' + divId).css('padding-left')) -
        parseFloat($('#' + divId).css('padding-right')),
      itemPadding =
        parseFloat($('#' + divId + ' > nav').css('padding-left')) +
        parseFloat($('#' + divId + ' > nav').css('padding-right')),
      itemMargin =
        parseFloat($('#' + divId + ' > nav').css('margin-left')) +
        parseFloat($('#' + divId + ' > nav').css('margin-right')),
      itemActualMinWidth =
        parseFloat($('#' + divId + ' > nav').css('min-width')) +
        itemPadding +
        itemMargin,
      items = $('#' + divId + ' > nav').filter(function () {
        return $(this).css('display') !== 'none';
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
    $('#' + divId + ' > nav').css(
      'width',
      Math.floor(containerWidth / horizontalCapacity - itemPadding - itemMargin)
    );

    // Let elements in each row has the same height.
    if (horizontalCapacity > 1) {
      for (var i = 0; i < itemLength; i += horizontalCapacity) {
        var maxHeight = 0;
        for (var j = 0; j < horizontalCapacity; j++) {
          var height = parseFloat(items.eq(i + j).css('height'));

          if (height > maxHeight) {
            maxHeight = height;
          }
        }
        for (var j = 0; j < horizontalCapacity; j++) {
          items.eq(i + j).css('min-height', maxHeight);
        }
      }
    } else {
      items.css('min-height', 'auto');
    }
  }

  $('#fw-menu-toggle').click(function () {
    $('#fw-menu').toggle();
    menuResize('fw-menu');
    $('#fw-menu-toggle .badge').removeClass('active');
  });
  OO.ui.infuse($('#p-menu-toggle')).on('click', function () {
    $('#p-actions-and-toolbox').toggle();
    menuResize('p-actions-and-toolbox');
  });
  $(window).resize(function () {
    menuResize('fw-menu');
    menuResize('p-actions-and-toolbox');
  });
}

module.exports = Object.freeze({ init: init });
