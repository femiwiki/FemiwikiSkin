<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use MediaWiki\Skins\Femiwiki\Constants;
use OutputPage;

class Width implements \MediaWiki\Hook\OutputPageBodyAttributesHook {
	/**
	 * @inheritDoc
	 */
	public function onOutputPageBodyAttributes( $out, $sk, &$bodyAttrs ): void {
		if ( $sk->getSkinName() !== Constants::SKIN_NAME || !self::isWide( $out ) ) {
			return;
		}
		$bodyAttrs['class'] ??= '';
		$bodyAttrs['class'] .= ' fw-wide-content';
	}

	/**
	 * The pages Vector 2022 leaves out of its limited width by default
	 * ($wgVectorMaxWidthOptions), except the main page.
	 */
	private static function isWide( OutputPage $out ): bool {
		$title = $out->getTitle();
		if ( !$title || $title->isSpecial( 'Preferences' ) ) {
			return false;
		}
		if ( $title->inNamespaces( NS_SPECIAL, NS_CATEGORY ) ) {
			return true;
		}
		$request = $out->getRequest();
		return in_array( $request->getRawVal( 'action' ), [ 'history', 'edit', 'submit' ], true )
			|| ( $request->getRawVal( 'diff' ) ?? '' ) !== '';
	}
}
