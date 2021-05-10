function init() {
  /** @type {JQuery<HTMLInputElement>} */ var searchInput = $('#searchInput');
  /** @type {JQuery<HTMLButtonElement>} */ var searchClearButton =
    $('#searchClearButton');

  searchInput.on('input', function () {
    searchClearButton.toggle(!!this.value);
  });
  searchClearButton.click(function () {
    searchInput.val('').trigger('input').focus();
  });
}

module.exports = Object.freeze({ init: init });
