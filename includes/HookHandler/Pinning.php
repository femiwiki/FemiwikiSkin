<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use MediaWiki\Skins\Femiwiki\Constants;

class Pinning implements \MediaWiki\Preferences\Hook\GetPreferencesHook {
	/**
	 * The buttons beside the ToC and the right column save these, so Special:Preferences does
	 * not show them.
	 * @inheritDoc
	 */
	public function onGetPreferences( $user, &$preferences ) {
		foreach ( array_keys( Constants::PINNED_CLIENTPREFS ) as $option ) {
			$preferences[$option] = [ 'type' => 'api' ];
		}
	}
}
