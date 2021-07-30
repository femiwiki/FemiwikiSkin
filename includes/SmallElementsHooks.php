<?php

namespace MediaWiki\Skins\Femiwiki;

class SmallElementsHooks implements \MediaWiki\Preferences\Hook\GetPreferencesHook {
	/**
	 * @inheritDoc
	 */
	public function onGetPreferences( $user, &$preferences ) {
		$newPrefs = [
			Constants::PREF_KEY_LARGER_ELEMENTS => [
				'type' => 'toggle',
				'label-message' => 'prefs-femiwiki-large-elements-label',
				'help-message' => 'prefs-femiwiki-large-elements-help',
				'section' => 'rendering/skin/skin-prefs',
				'hide-if' => [ '!==', 'wpskin', Constants::SKIN_NAME ],
			]
		];

		$skinSectionIndex = array_search( 'skin', array_keys( $preferences ) );
		if ( $skinSectionIndex !== false ) {
			$newSectionIndex = $skinSectionIndex + 1;
			$preferences = array_slice( $preferences, 0, $newSectionIndex, true )
				+ $newPrefs
				+ array_slice( $preferences, $newSectionIndex, null, true );
		} else {
			$preferences += $newPrefs;
		}
	}
}
