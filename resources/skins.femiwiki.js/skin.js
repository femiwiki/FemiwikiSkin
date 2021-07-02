/** @type {CheckboxHack} */ var checkboxHack = require(/** @type {string} */ (
  'mediawiki.page.ready'
)).checkboxHack;

/**
 * Improve the interactivity of the sidebar panel by binding optional checkbox hack enhancements
 * for focus and `aria-expanded`. Also, flip the icon image on click.
 *
 * @param {HTMLElement|null} checkbox
 * @param {HTMLElement|null} button
 * @return {void}
 */
function initCheckboxHack(checkbox, button) {
  if (checkbox instanceof HTMLInputElement && button) {
    checkboxHack.bindToggleOnClick(checkbox, button);
    checkboxHack.bindUpdateAriaExpandedOnInput(checkbox, button);
    checkboxHack.updateAriaExpanded(checkbox, button);
    checkboxHack.bindToggleOnSpaceEnter(checkbox, button);
  }
}

/**
 * @return {void}
 */
function main() {
  require('./discussionStatus.js').init();
  require('./notificationBadge.js').init();
  require('./searchClearButton.js').init();
  require('./watchingUsers.js').init();

  initCheckboxHack(
    window.document.getElementById('fw-menu-checkbox'),
    window.document.getElementById('fw-menu-toggle')
  );
  initCheckboxHack(
    window.document.getElementById('fw-page-menu-checkbox'),
    window.document.getElementById('p-menu-toggle')
  );

  var fwMenuToggle = document.querySelector('#fw-menu-toggle');
  var fwNotificationBadge = document.querySelector('#fw-menu-toggle .badge');
  if (fwMenuToggle && fwNotificationBadge) {
    fwMenuToggle.addEventListener('click', function () {
      // @ts-ignore: fwNotificationBadge is possibly 'null'.
      fwNotificationBadge.classList.remove('active');
    });
  }
}

main();
