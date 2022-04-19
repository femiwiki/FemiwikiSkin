<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use ExtensionRegistry;

class Notification implements \MediaWiki\Hook\UserMailerTransformContentHook {

	/**
	 * Echo(REL1_31)'s content values
	 * @See https://gerrit.wikimedia.org/r/plugins/gitiles/mediawiki/extensions/Echo/+/REL1_31/includes/formatters/EchoHtmlEmailFormatter.php
	 */
	private const PRIMARY_LINK_STYLE = 'cursor:pointer; text-align:center; text-decoration:none; padding:.45em 0.6em .45em; color:#FFF; background:#36C; font-family: Arial, Helvetica, sans-serif;font-size: 13px;';
	private const FOOTER_PREFERENCE_LINK_STYLE = 'text-decoration: none; color: #36C;';

	/**
	 * Styles for Femiwiki
	 */
	private const FEMIWIKI_PRIMARY_LINK_STYLE = 'cursor:pointer; text-align:center; text-decoration:none; padding:.45em 0.6em .45em; color:#FFF; background:#aca7e2; font-family: Arial, Helvetica, sans-serif;font-size: 13px;';
	private const FEMIWIKI_FOOTER_PREFERENCE_LINK_STYLE = 'text-decoration: none; color: #5144a3;';

	/**
	 * Modifying HTML mails sent from Echo.
	 * @inheritDoc
	 */
	public function onUserMailerTransformContent( $to, $from, &$body, &$error ) {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) || !is_array( $body ) || !isset( $body['html'] ) ) {
			return;
		}

		$body['html'] = str_replace(
			[ self::PRIMARY_LINK_STYLE,
				self::FOOTER_PREFERENCE_LINK_STYLE ],
			[ self::FEMIWIKI_PRIMARY_LINK_STYLE,
				self::FEMIWIKI_FOOTER_PREFERENCE_LINK_STYLE ],
			$body['html']
		);
	}
}
