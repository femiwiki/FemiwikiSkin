'use strict';

import * as Api from 'wdio-mediawiki/Api';
import * as Femiwiki from '../pageobjects/femiwiki.page';
import EchoPage from '../pageobjects/echo.page';
import * as Util from 'wdio-mediawiki/Util';
import assert from 'assert';
import * as UserLoginPage from 'wdio-mediawiki/LoginPage';
import * as UserPreferences from '../userpreferences';
import * as BlankPage from 'wdio-mediawiki/BlankPage';
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
