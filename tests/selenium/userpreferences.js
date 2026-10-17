import BlankPage from "wdio-mediawiki/BlankPage.js";
import { waitForModuleState } from "wdio-mediawiki/Util.js";

class UserPreferences {
	async setPreferences(preferences) {
		await BlankPage.open();
		await waitForModuleState("mediawiki.base");

		return browser.execute(
			(prefs) =>
				mw.loader
					.using("mediawiki.api")
					.then(() => new mw.Api().saveOptions(prefs)),
			preferences,
		);
	}

	async enableFemiwiki() {
		await this.setPreferences({
			skin: "femiwiki",
		});
	}
}

export default new UserPreferences();
