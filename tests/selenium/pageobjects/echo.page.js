'use strict';
const Page = require('wdio-mediawiki/Page');

class EchoPage extends Page {
  get notifications() {
    return $('#pt-notifications-all');
  }
  get popup() {
    return $('.oo-ui-labelElement-label');
  }
}
module.exports = new EchoPage();
