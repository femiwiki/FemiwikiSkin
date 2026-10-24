<?php
namespace MediaWiki\Skins\Femiwiki;

/**
 * @internal Only for use by Femiwiki skin.
 */
final class Registration {
	/**
	 * Has WikidataPageBanner put the banner into the site notice, which this skin draws above
	 * the page title, instead of the subtitle below the page tools.
	 */
	public static function onRegistration(): void {
		$skins = &$GLOBALS['wgWPBDisplaySubtitleAfterBannerSkins'];
		if ( is_array( $skins ) && !in_array( Constants::SKIN_NAME, $skins, true ) ) {
			$skins[] = Constants::SKIN_NAME;
		}
	}
}
