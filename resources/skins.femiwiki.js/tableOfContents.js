var CURRENT_CLASS = 'fw-toc-current';

/**
 * Marks the section the reader is in, and closes the drawer once a section is picked.
 *
 * @return {void}
 */
function init() {
  var toc = document.getElementById('fw-toc');
  var checkbox = document.getElementById('fw-toc-checkbox');
  if (!toc || !(checkbox instanceof HTMLInputElement)) {
    return;
  }
  var tocCheckbox = checkbox;
  var panel = toc.querySelector('.fw-toc-panel');
  var top = toc.querySelector('.fw-toc-top');

  /** @type {{heading: HTMLElement, item: Element}[]} */
  var sections = [];
  var links = toc.querySelectorAll('.fw-toc-link[data-anchor]');
  for (var i = 0; i < links.length; i++) {
    var anchor = links[i].getAttribute('data-anchor');
    var heading = anchor && document.getElementById(anchor);
    if (heading && links[i].parentElement) {
      sections.push({
        heading: heading,
        item: /** @type {Element} */ (links[i].parentElement),
      });
    }
  }

  toc.addEventListener('click', function (event) {
    var target = /** @type {Element} */ (event.target);
    if (
      target.closest &&
      target.closest('.fw-toc-link') &&
      tocCheckbox.checked
    ) {
      tocCheckbox.checked = false;
      tocCheckbox.dispatchEvent(new Event('input'));
    }
  });

  /** @type {Element|null} */
  var current = null;

  /**
   * @param {Element|null} item
   * @return {void}
   */
  function setCurrent(item) {
    if (item === current) {
      return;
    }
    if (current) {
      current.classList.remove(CURRENT_CLASS);
    }
    current = item;
    if (!item) {
      return;
    }
    item.classList.add(CURRENT_CLASS);
    // Keep the mark in view in a ToC taller than the screen, without scrolling the page.
    if (panel && panel.scrollHeight > panel.clientHeight) {
      var link = item.querySelector('.fw-toc-link') || item;
      var linkRect = link.getBoundingClientRect();
      var panelRect = panel.getBoundingClientRect();
      if (linkRect.top < panelRect.top || linkRect.bottom > panelRect.bottom) {
        panel.scrollTop +=
          linkRect.top - panelRect.top - panel.clientHeight / 2;
      }
    }
  }

  var scheduled = false;

  /**
   * @return {void}
   */
  function update() {
    scheduled = false;
    // A section counts as read once its heading passes the upper third of the screen.
    var line = window.innerHeight / 3;
    var item = top;
    for (var j = 0; j < sections.length; j++) {
      if (sections[j].heading.getBoundingClientRect().top > line) {
        break;
      }
      item = sections[j].item;
    }
    setCurrent(item);
  }

  /**
   * @return {void}
   */
  function schedule() {
    if (!scheduled) {
      scheduled = true;
      window.requestAnimationFrame(update);
    }
  }

  window.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('resize', schedule);
  update();
}

module.exports = Object.freeze({ init: init });
