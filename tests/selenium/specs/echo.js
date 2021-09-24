'use strict';

const Api = require('wdio-mediawiki/Api');
const Femiwiki = require('../pageobjects/femiwiki.page');
const EchoPage = require('../pageobjects/echo.page');
const Util = require('wdio-mediawiki/Util');
const assert = require('assert');
const UserLoginPage = require('wdio-mediawiki/LoginPage');
const UserPreferences = require('../userpreferences');
const BlankPage = require('wdio-mediawiki/BlankPage');
describe('flyout for notifications appears when clicked @daily', function () {
  let bot;

  before(async () => {
    bot = await Api.bot();
  });

  it('checks for OOUI icon replacement @daily', function () {
    // Prepares accounts
    const username = Util.getTestString('User-');
    const password = Util.getTestString();
    browser.call(async () => {
      await Api.createAccount(bot, username, password);
    });

    UserLoginPage.login(username, password);
    UserPreferences.enableFemiwiki();
    // Refresh after changing the preference
    BlankPage.open();
    Femiwiki.gnbMenu.click();
    EchoPage.notifications.waitForDisplayed();
    EchoPage.notifications.click();
    EchoPage.popup.waitForDisplayed();
    assert(EchoPage.popup.isExisting());
  });
});
