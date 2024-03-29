<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use Config;
use ConfigFactory;
use MediaWiki\Skins\Femiwiki\Constants;

class DefaultHooks implements
	\MediaWiki\Linker\Hook\HtmlPageLinkRendererBeginHook,
	\MediaWiki\ResourceLoader\Hook\ResourceLoaderGetConfigVarsHook
	{

	private ConfigFactory $configFactory;

	public function __construct( ConfigFactory $configFactory ) {
		$this->configFactory = $configFactory;
	}

	/**
	 * exports static key and IDs to JavaScript.
	 * @inheritDoc
	 */
	public function onResourceLoaderGetConfigVars( array &$vars, $skin, Config $config ): void {
		$firebaseKey = $config->get( Constants::CONFIG_FIREBASE_KEY );

		$vars['wgFemiwikiFirebaseKey'] = $firebaseKey;
	}

	/**
	 * Adds default class to links which are not either 'external', 'stub', 'interwiki', etc to
	 * apply styles to all links.
	 *
	 * @inheritDoc
	 */
	public function onHtmlPageLinkRendererBegin( $linkRenderer, $target, &$text,
		&$customAttribs, &$query, &$ret
	) {
		$fwConfig = $this->configFactory->makeConfig( Constants::CONFIG_NAME );
		if ( !$fwConfig->get( Constants::CONFIG_ADD_LINK_CLASS ) ) {
			return;
		}

		if ( isset( $customAttribs['class'] ) ) {
			if ( is_array( $customAttribs['class'] ) ) {
				$customAttribs['class'][] = 'fw-link';
			} else {
				$customAttribs['class'] .= ' fw-link';
			}
		} else {
			$customAttribs['class'] = 'fw-link';
		}
	}
}
