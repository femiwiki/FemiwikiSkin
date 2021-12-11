<?php

namespace MediaWiki\Skins\Femiwiki;

use MediaWiki\MediaWikiServices;

class SmallElementsHooks implements
	\MediaWiki\Preferences\Hook\GetPreferencesHook,
	\MediaWiki\Hook\OutputPageBodyAttributesHook
	{
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

	/**
	 * @inheritDoc
	 */
	public function onOutputPageBodyAttributes( $out, $sk, &$bodyAttrs ): void {
		$user = $sk->getUser();
		$registered = $user->isRegistered();
		$config = $sk->getConfig();
		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();

		if (
			( !$registered && $config->get( Constants::CONFIG_KEY_SMALL_ELEMENTS_FOR_ANONYMOUS_USER ) )
			|| ( $registered && !$userOptionsLookup->getBoolOption( $user, Constants::PREF_KEY_LARGER_ELEMENTS ) )
			) {
			$bodyAttrs['class'] = $bodyAttrs['class'] ?? '';
			$bodyAttrs['class'] .= ' fw-legacy-small-elements';
		}
	}
}
