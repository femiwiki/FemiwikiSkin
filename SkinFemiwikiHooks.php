<?php

/**
 * SkinFemiwikiHooks class for the Femiwiki skin hooks
 *
 */
class SkinFemiwikiHooks {

	/**
	 * Echo(REL1_31)'s contant values
	 * @See https://gerrit.wikimedia.org/r/plugins/gitiles/mediawiki/extensions/Echo/+/REL1_31/includes/formatters/EchoHtmlEmailFormatter.php
	 */
	const PRIMARY_LINK_STYLE = 'cursor:pointer; text-align:center; text-decoration:none; padding:.45em 0.6em .45em; color:#FFF; background:#36C; font-family: Arial, Helvetica, sans-serif;font-size: 13px;';
	const FOOTER_PREFERENCE_LINK_STYLE = 'text-decoration: none; color: #36C;';

	/**
	 * Styles for Femiwiki
	 */
	const FEMIWIKI_PRIMARY_LINK_STYLE = 'cursor:pointer; text-align:center; text-decoration:none; padding:.45em 0.6em .45em; color:#FFF; background:#aca7e2; font-family: Arial, Helvetica, sans-serif;font-size: 13px;';
	const FEMIWIKI_FOOTER_PREFERENCE_LINK_STYLE = 'text-decoration: none; color: #5144a3;';

	/**
	 * Modifing HTML mails sent from Echo.
	 * @param MailAddress[] $to Array of recipients' email addresses
	 * @param MailAddress $from Sender's email
	 * @param string|string[] &$body Email's text or Array of two strings to be the text and html bodies
	 * @param false &$error 이 함수가 false를 반환하고, true로 평가되는 값을 이 변수에 집어넣으면 에러메시지에 이 변수가 출력된다
	 * @return bool
	 */
	public static function onUserMailerTransformContent( array $to, $from, &$body, &$error ) {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) || !is_array( $body ) || !isset( $body['html'] ) ) {
			return false;
		}

		$body['html'] = str_replace(
			[ self::PRIMARY_LINK_STYLE,
				self::FOOTER_PREFERENCE_LINK_STYLE ],
			[ self::FEMIWIKI_PRIMARY_LINK_STYLE,
				self::FEMIWIKI_FOOTER_PREFERENCE_LINK_STYLE ],
			$body['html']
		);

		return true;
	}

	/**
	 * export static key and id to JavaScript
	 * @param array &$vars Array of variables to be added into the output of the startup module.
	 * @return true
	 */
	public static function onResourceLoaderGetConfigVars( &$vars ) {
		global $wgFirebaseKey, $wgFacebookAppId;

		$vars['wgFirebaseKey'] = $wgFirebaseKey;
		$vars['wgFacebookAppId'] = $wgFacebookAppId;

		return true;
	}
}
