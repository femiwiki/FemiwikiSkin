import { createApiClient } from 'wdio-mediawiki/Api.js';
import Femiwiki from '../pageobjects/femiwiki.page.js';
import EchoPage from '../pageobjects/echo.page.js';
import { getTestString } from 'wdio-mediawiki/Util.js';
import assert from 'assert';
import UserLoginPage from 'wdio-mediawiki/LoginPage.js';
import UserPreferences from '../userpreferences.js';
import BlankPage from 'wdio-mediawiki/BlankPage.js';

describe('flyout for notifications appears when clicked @daily', () => {
  let api;

  before(async () => {
    api = await createApiClient();
  });

  it('checks for OOUI icon replacement @daily', async () => {
    // Prepares accounts
    const username = getTestString('User-');
    const password = getTestString();
    await api.createAccount(username, password);

    await UserLoginPage.login(username, password);
    await UserPreferences.enableFemiwiki();
    // Refresh after changing the preference
    await BlankPage.open();
    await Femiwiki.gnbMenu.click();
    await EchoPage.notifications.waitForDisplayed();
    await EchoPage.notifications.click();
    await EchoPage.popup.waitForDisplayed();
    assert(await EchoPage.popup.isExisting());
  });
});
