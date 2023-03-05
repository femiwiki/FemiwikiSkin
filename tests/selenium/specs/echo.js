'use strict';

const Api = require('wdio-mediawiki/Api');
const Femiwiki = require('../pageobjects/femiwiki.page');
const EchoPage = require('../pageobjects/echo.page');
const Util = require('wdio-mediawiki/Util');
const assert = require('assert');
const UserLoginPage = require('wdio-mediawiki/LoginPage');
const UserPreferences = require('../userpreferences');
const BlankPage = require('wdio-mediawiki/BlankPage');
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
