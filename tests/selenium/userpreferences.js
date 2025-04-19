'use strict';

import * as BlankPage from 'wdio-mediawiki/BlankPage';
import * as Util from 'wdio-mediawiki/Util';

class UserPreferences {
  async setPreferences(preferences) {
    await BlankPage.open();
    await Util.waitForModuleState('mediawiki.base');

    return browser.execute(function (prefs) {
      return mw.loader.using('mediawiki.api').then(function () {
        return new mw.Api().saveOptions(prefs);
      });
    }, preferences);
  }

  async enableFemiwiki() {
    await this.setPreferences({
      skin: 'femiwiki',
    });
  }
}

module.exports = new UserPreferences();
