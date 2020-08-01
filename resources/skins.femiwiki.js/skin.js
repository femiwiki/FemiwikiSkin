/**
 * @return {void}
 */
function main() {
  require('./searchClearButton.js').init();
  require('./notificationBadge.js').init();

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
