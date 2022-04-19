<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skins\Femiwiki\SkinFemiwiki;

class Mobile implements \MediaWiki\Hook\BeforePageDisplayHook {
	/**
	 * Shows a message that says you can't that on mobile to the user if the user has tried to edit
	 * a page. This is because of https://phabricator.wikimedia.org/T257746
	 * @inheritDoc
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		if ( !$skin instanceof SkinFemiwiki || !ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' ) ) {
			return;
		}

		$context = MediaWikiServices::getInstance()->getService( 'MobileFrontend.Context' );
		if ( !$context->shouldDisplayMobileView() ) {
			return;
		}

		$out->addModules( [ 'skins.femiwiki.mobile.js' ] );
	}
}
