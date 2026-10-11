var PINNED_CLASS = 'fw-nav-bar-pinned';
var SHOWN_CLASS = 'fw-nav-bar-shown';
var ANIMATE_CLASS = 'fw-nav-bar-animate';
var TOC_CLASS = 'fw-nav-bar-toc';
// @width-breakpoint-toc-column, from where the ToC column follows the scroll instead
var TOC_COLUMN_QUERY = '(min-width: 1120px)';
// Long enough for the scroll that a click on a section link starts
var JUMP_MS = 200;

/**
 * Brings the top bar back when the reader scrolls up, where the ToC is a drawer (#503).
 *
 * The bar keeps its place in the page and is pinned only while out of view, so pinning moves
 * nothing. Like Vector's sticky header it slides with a transform, and scroll-padding-top keeps
 * section links clear of it. A jump to a section is not a scroll up, so it hides the bar.
 *
 * @return {void}
 */
function init() {
  var bar = document.querySelector('.nav-bar');
  var action = mw.config.get('wgAction');
  if (
    !(bar instanceof HTMLElement) ||
    action === 'edit' ||
    action === 'submit'
  ) {
    return;
  }
  var navBar = bar;
  var classList = document.body.classList;
  var toColumn = window.matchMedia(TOC_COLUMN_QUERY);
  var search = document.getElementById('p-search');
  var menuCheckbox = document.getElementById('fw-menu-checkbox');
  var tocCheckbox = document.getElementById('fw-toc-checkbox');
  var tocToggle = document.getElementById('fw-nav-toc-toggle');
  var titleButtons = document.getElementById('p-title-buttons');
  var height = navBar.offsetHeight;
  var lastY = window.scrollY;
  var upDistance = 0;
  var jumped = false;
  var paused = false;
  var frame = 0;

  /**
   * @return {void}
   */
  function unpin() {
    classList.remove(PINNED_CLASS, SHOWN_CLASS, ANIMATE_CLASS);
  }

  /**
   * @return {void}
   */
  function show() {
    if (!classList.contains(PINNED_CLASS)) {
      classList.add(PINNED_CLASS);
      // Lay the bar out above the screen first, so it slides in from there
      navBar.getBoundingClientRect();
      classList.add(ANIMATE_CLASS);
    }
    // The bar is out of sight here, so the button comes and goes without moving anything
    if (tocToggle && titleButtons) {
      classList.toggle(
        TOC_CLASS,
        titleButtons.getBoundingClientRect().bottom <= height,
      );
    }
    classList.add(SHOWN_CLASS);
  }

  /**
   * @return {boolean} The reader is using the bar, a menu or the drawer
   */
  function isBusy() {
    return (
      (menuCheckbox instanceof HTMLInputElement && menuCheckbox.checked) ||
      (tocCheckbox instanceof HTMLInputElement && tocCheckbox.checked) ||
      (!!search && search.contains(document.activeElement))
    );
  }

  /**
   * @return {void}
   */
  function update() {
    frame = 0;
    var y = window.scrollY;
    var delta = y - lastY;
    lastY = y;
    if (paused || toColumn.matches || isBusy()) {
      upDistance = 0;
      return;
    }
    var shown = classList.contains(SHOWN_CLASS);
    if (jumped || delta > 0) {
      jumped = false;
      upDistance = 0;
      if (y >= height) {
        classList.remove(SHOWN_CLASS);
        return;
      }
    }
    // Where the bar's own place is in view, it stays there
    if (y <= 0 || (y < height && !shown)) {
      unpin();
      upDistance = 0;
    } else if (delta < 0) {
      upDistance -= delta;
      if (!shown && upDistance >= height) {
        show();
      }
    }
  }

  window.addEventListener(
    'scroll',
    function () {
      if (!frame) {
        frame = window.requestAnimationFrame(update);
      }
    },
    { passive: true },
  );
  toColumn.addEventListener('change', unpin);

  document.addEventListener('click', function (event) {
    var target = event.target;
    if (
      !event.defaultPrevented &&
      target instanceof Element &&
      target.closest('a[href^="#"]')
    ) {
      jumped = true;
      window.setTimeout(function () {
        jumped = false;
      }, JUMP_MS);
    }
  });

  // Tabbing back into the bar brings it into view
  navBar.addEventListener('focusin', function () {
    if (
      window.scrollY >= height &&
      !toColumn.matches &&
      !classList.contains(SHOWN_CLASS)
    ) {
      show();
    }
  });

  if (tocToggle && tocCheckbox instanceof HTMLInputElement) {
    var checkbox = tocCheckbox;
    tocToggle.addEventListener('click', function () {
      checkbox.checked = !checkbox.checked;
      checkbox.dispatchEvent(new Event('input'));
    });
  }

  // VisualEditor's toolbar takes the top of the screen while it is open
  mw.hook('ve.activationStart').add(function () {
    paused = true;
    unpin();
  });
  mw.hook('ve.deactivationComplete').add(function () {
    paused = false;
    lastY = window.scrollY;
  });
}

module.exports = Object.freeze({ init: init });
