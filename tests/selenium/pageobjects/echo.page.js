'use strict';
import Page from 'wdio-mediawiki/Page';

class EchoPage extends Page {
  get notifications() {
    return $('#pt-notifications-all');
  }
  get popup() {
    return $('.oo-ui-labelElement-label');
  }
}
export default new EchoPage();
