'use strict';

const BlankPage = require('wdio-mediawiki/BlankPage');
const Util = require('wdio-mediawiki/Util');

class UserPreferences {
  setPreferences(preferences) {
    BlankPage.open();
    Util.waitForModuleState('mediawiki.base');

    return browser.execute(function (prefs) {
      return mw.loader.using('mediawiki.api').then(function () {
        return new mw.Api().saveOptions(prefs);
      });
    }, preferences);
  }

  enableFemiwiki() {
    this.setPreferences({
      skin: 'femiwiki',
    });
  }
}

module.exports = new UserPreferences();
