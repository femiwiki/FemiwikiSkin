'use strict';
const Page = require('wdio-mediawiki/Page');

class FemiwikiPage extends Page {
  get gnbMenu() {
    return $('#fw-menu-toggle');
  }
}
module.exports = new FemiwikiPage();
