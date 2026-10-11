/** @type {CheckboxHack} */ var checkboxHack = require(
  /** @type {string} */ ('mediawiki.page.ready'),
).checkboxHack;

/**
 * Close the menu on a click or focus outside it, on Escape and on picking an item, as Vector's
 * dropdowns and Codex menus do. Escape and picking an item return focus to the button.
 *
 * @param {HTMLInputElement} checkbox
 * @param {HTMLElement} button
 * @param {HTMLElement} target
 * @return {void}
 */
function bindDismiss(checkbox, button, target) {
  /**
   * @param {boolean} refocus
   * @return {void}
   */
  function close(refocus) {
    if (!checkbox.checked) {
      return;
    }
    checkbox.checked = false;
    // checkboxHack updates aria-expanded on this event.
    checkbox.dispatchEvent(new Event('input', { bubbles: true }));
    if (refocus) {
      button.focus();
    }
  }

  /**
   * @param {Event} event
   * @return {void}
   */
  function closeIfOutside(event) {
    var node = event.target;
    if (
      node instanceof Element &&
      !checkbox.contains(node) &&
      !button.contains(node) &&
      !target.contains(node) &&
      // The Echo flyout opens from the site menu but is drawn on the body.
      !node.closest('.mw-echo-ui-overlay')
    ) {
      close(false);
    }
  }

  window.addEventListener('click', closeIfOutside, true);
  window.addEventListener('focusin', closeIfOutside, true);

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !event.defaultPrevented) {
      close(true);
    }
  });

  target.addEventListener('click', function (event) {
    var link = event.target instanceof Element && event.target.closest('a');
    // The Echo badge opens its flyout, anchored to the badge.
    if (link && !link.classList.contains('mw-echo-notifications-badge')) {
      close(true);
    }
  });
}

/**
 * Improve the interactivity of the sidebar panel by binding optional checkbox hack enhancements
 * for focus and `aria-expanded`. Given a menu, also close it as a menu.
 *
 * @param {HTMLElement|null} checkbox
 * @param {HTMLElement|null} button
 * @param {HTMLElement|null} [menu]
 * @return {void}
 */
function initCheckboxHack(checkbox, button, menu) {
  if (checkbox instanceof HTMLInputElement && button) {
    checkboxHack.bindToggleOnClick(checkbox, button);
    checkboxHack.bindUpdateAriaExpandedOnInput(checkbox);
    checkboxHack.updateAriaExpanded(checkbox);
    checkboxHack.bindToggleOnEnter(checkbox);
    if (menu) {
      bindDismiss(checkbox, button, menu);
    }
  }
}

/**
 * @return {void}
 */
function main() {
  require('./discussionStatus.js').init();
  require('./notificationBadge.js').init();
  require('./watchingUsers.js').init();
  require('./tableOfContents.js').init();

  initCheckboxHack(
    window.document.getElementById('fw-toc-checkbox'),
    window.document.getElementById('fw-toc-toggle'),
  );
  initCheckboxHack(
    window.document.getElementById('fw-menu-checkbox'),
    window.document.getElementById('fw-menu-toggle'),
    window.document.getElementById('fw-menu'),
  );
  initCheckboxHack(
    window.document.getElementById('fw-page-menu-checkbox'),
    window.document.getElementById('p-menu-toggle'),
    window.document.getElementById('p-actions-and-toolbox'),
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
