'use strict';

import * as Api from 'wdio-mediawiki/Api.js';
import * as Femiwiki from '../pageobjects/femiwiki.page.js';
import EchoPage from '../pageobjects/echo.page.js';
import * as Util from 'wdio-mediawiki/Util.js';
import assert from 'assert';
import * as UserLoginPage from 'wdio-mediawiki/LoginPage.js';
import * as UserPreferences from '../userpreferences.js';
import * as BlankPage from 'wdio-mediawiki/BlankPage.js';
describe('flyout for notifications appears when clicked @daily', () => {
  let bot;

  before(async () => {
    bot = await Api.bot();
  });

  it('checks for OOUI icon replacement @daily', async () => {
    // Prepares accounts
    const username = Util.getTestString('User-');
    const password = Util.getTestString();
    browser.call(async () => {
      await Api.createAccount(bot, username, password);
    });

    await UserLoginPage.login(username, password);
    await UserPreferences.enableFemiwiki();
    // Refresh after changing the preference
    await BlankPage.open();
    await Femiwiki.gnbMenu.click();
    await EchoPage.notifications.waitForDisplayed();
    await EchoPage.notifications.click();
    await EchoPage.popup.waitForDisplayed();
    assert(EchoPage.popup.isExisting());
  });
});
