'use strict';
import Page from 'wdio-mediawiki/Page';

class FemiwikiPage extends Page {
  get gnbMenu() {
    return $('#fw-menu-toggle');
  }
}
export default new FemiwikiPage();
