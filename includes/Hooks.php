<?php

namespace MediaWiki\Skins\Femiwiki;

use EchoNotificationController;
use EchoSeenTime;
use ExtensionRegistry;
use MailAddress;
use MediaWiki\MediaWikiServices;
use MWEchoNotifUser;
use OutputPage;
use Skin;
use SkinTemplate;
use SpecialPage;
use Title;

/**
 * Hooks class for the Femiwiki skin hooks
 *
 */
class Hooks implements \MediaWiki\Linker\Hook\HtmlPageLinkRendererBeginHook {
	/**
	 * @param OutputPage $output The page view.
	 * @param Skin $skin The skin that's going to build the UI.
	 */
	public static function onBeforePageDisplay( OutputPage $output, Skin $skin ) {
		if ( !$skin instanceof SkinFemiwiki || !ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' ) ) {
			return;
		}

		$context = MediaWikiServices::getInstance()->getService( 'MobileFrontend.Context' );
		if ( !$context->shouldDisplayMobileView() ) {
			return;
		}

		$output->addModules( [ 'skins.femiwiki.mobile.js' ] );
	}

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
	 * @param MailAddress[] $to Array of recipients' email addresses
	 * @param MailAddress $from Sender's email
	 * @param string|string[] &$body Email's text or Array of two strings to be the text and html bodies
	 * @param false &$error 이 함수가 false를 반환하고, true로 평가되는 값을 이 변수에 집어넣으면 에러메시지에 이 변수가 출력된다
	 * @return bool
	 */
	public static function onUserMailerTransformContent( array $to, $from, &$body, &$error ) {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) || !is_array( $body ) || !isset( $body['html'] ) ) {
			return true;
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
		global $wgFemiwikiFirebaseKey, $wgFemiwikiFacebookAppId, $wgFemiwikiAddThisId;

		$vars['wgFemiwikiFirebaseKey'] = $wgFemiwikiFirebaseKey;
		$vars['wgFemiwikiFacebookAppId'] = $wgFemiwikiFacebookAppId;
		if ( $wgFemiwikiAddThisId ) {
			$vars['wgFemiwikiUseAddThis'] = true;
			if ( is_array( $wgFemiwikiAddThisId ) && isset( $wgFemiwikiAddThisId['tool'] ) ) {
				$vars['wgFemiwikiAddThisToolId'] = $wgFemiwikiAddThisId['tool'];
			}
		}

		return true;
	}

	/**
	 * Handler for PersonalUrls hook.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/PersonalUrls
	 * @param array &$personalTools Array of URLs to append to.
	 * @param Title &$title Title of page being visited.
	 * @param SkinTemplate $sk
	 * @return bool true in all cases
	 */
	public static function onPersonalUrls( &$personalTools, &$title, $sk ) {
		if ( !$sk instanceof SkinFemiwiki ) {
			return true;
		}
		self::addNotification( $personalTools, $title, $sk );
		self::addMobileOptions( $personalTools, $title, $sk );

		return true;
	}

	/**
	 * Add a "Notifications" item to the user toolbar ('personal URLs').
	 * @param array &$personalTools Array of URLs to append to.
	 * @param Title &$title Title of page being visited.
	 * @param SkinTemplate $sk
	 * @return void
	 */
	private static function addNotification( &$personalTools, &$title, $sk ) {
		if ( !$sk instanceof SkinFemiwiki || !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			return;
		}

		$user = $sk->getUser();
		if ( $user->isAnon() ) {
			return;
		}

		$title = $sk->getTitle();

		$notifUser = MWEchoNotifUser::newFromUser( $user );
		$count = $notifUser->getNotificationCount();
		$count = max( 0, $count );

		$msgNotificationTimestamp = $notifUser->getLastUnreadMessageTime();
		$alertNotificationTimestamp = $notifUser->getLastUnreadAlertTime();

		$seenAlertTime = EchoSeenTime::newFromUser( $user )->getTime( 'alert', TS_ISO_8601 );
		$seenMsgTime = EchoSeenTime::newFromUser( $user )->getTime( 'message', TS_ISO_8601 );

		$formattedCount = EchoNotificationController::formatNotificationCount( $count );
		$msgText = $sk->msg( 'echo-notification-notice', $count );
		$url = SpecialPage::getTitleFor( 'Notifications' )->getLocalURL();
		$linkClasses = [ "mw-echo-notifications-badge", "mw-echo-notification-badge-fw-nojs" ];

		$hasUnseen = $count > 0 &&
			(
				$seenMsgTime !== false && $msgNotificationTimestamp !== false &&
				$seenMsgTime < $msgNotificationTimestamp->getTimestamp( TS_ISO_8601 )
			) ||
			(
				$seenAlertTime !== false && $alertNotificationTimestamp !== false &&
				$seenAlertTime < $alertNotificationTimestamp->getTimestamp( TS_ISO_8601 )
			);

		if ( $hasUnseen ) {
			$linkClasses[] = 'mw-echo-unseen-notifications';
		} elseif ( $count === 0 ) {
			$linkClasses[] = 'mw-echo-notifications-badge-all-read';
		}

		if ( $count > MWEchoNotifUser::MAX_BADGE_COUNT ) {
			$linkClasses[] = 'mw-echo-notifications-badge-long-label';
		}

		$insertUrls = [
			'notifications-all' => [
				'href' => $url,
				'text' => $msgText,
				'active' => ( $url == $title->getLocalUrl() ),
				'class' => $linkClasses,
				'data' => [
					'counter-num' => $count,
					'counter-text' => $formattedCount,
				],
			]
		];

		$personalTools = wfArrayInsertAfter( $personalTools, $insertUrls, 'userpage' );
	}

	/**
	 * Add a "MobileOptions" item to the user toolbar ('personal URLs').
	 * @param array &$personalTools Array of URLs to append to.
	 * @param Title &$title Title of page being visited.
	 * @param SkinTemplate $sk
	 */
	private static function addMobileOptions( &$personalTools, &$title, $sk ) {
		if ( !$sk instanceof SkinFemiwiki || !ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' ) ) {
			return;
		}

		$context = MediaWikiServices::getInstance()->getService( 'MobileFrontend.Context' );
		if ( !$context->shouldDisplayMobileView() ) {
			return;
		}

		$url = SpecialPage::getTitleFor( 'MobileOptions' )->getLocalURL();

		$insertUrls = [
			'mobile-preferences' => [
				'href' => $url,
				'text' => $sk->msg( 'prefs-mobile' )->text(),
				'active' => ( $url == $title->getLocalUrl() )
			]
		];

		if ( $sk->getUser()->isLoggedIn() && array_key_exists( 'preferences', $personalTools ) ) {
			$personalTools = wfArrayInsertAfter( $personalTools, $insertUrls, 'preferences' );
		} else {
			$personalTools = array_merge( $personalTools, $insertUrls );
		}
	}

	/**
	 * Convert the content model of a message that is actually JSON to JSON. This
	 * only affects validation and UI when saving and editing, not loading the
	 * content.
	 *
	 * @param Title $title
	 * @param string &$model
	 */
	public static function onContentHandlerDefaultModelFor( Title $title, &$model ) {
		if (
			$title->inNamespace( NS_MEDIAWIKI ) &&
			$title->getText() == 'skin-femiwiki-xeicon-map.json'
		) {
			$model = CONTENT_MODEL_JSON;
		}
	}

	/**
	 * @param SkinTemplate $sk
	 * @param array &$content_navigation
	 */
	public static function onSkinTemplateNavigation( $sk, &$content_navigation ) {
		if ( $sk->getSkinName() !== Constants::SKIN_NAME ) {
			return;
		}

		$title = $sk->getRelevantTitle();
		if ( $title && $title->canExist() ) {
			// Show the watch action anonymous users
			if ( !$sk->loggedin ) {
				$content_navigation['actions']['watch'] = [
					'class' => 'mw-watchlink-watch',
					'text' => $sk->msg( 'watch' )->text(),
					'href' => $title->getLocalURL( [ 'action' => 'watch' ] ),
					'data' => [
						'mw' => 'interface',
					],
				];
			}

			// Promote watch link from actions to views
			if ( isset( $content_navigation['actions']['watch'] ) ) {
				$key = 'watch';
			} elseif ( isset( $content_navigation['actions']['unwatch'] ) ) {
				$key = 'unwatch';
			} else {
				return;
			}

			$item = $content_navigation['actions'][$key];
			if ( !$item ) {
				return;
			}
			$content_navigation['namespaces'][$key] = $item;
			unset( $content_navigation['actions'][$key] );
		}
	}

	/**
	 * Add default class to links which are not either 'external', 'stub', 'interwiki' or etc.
	 * @inheritDoc
	 */
	public function onHtmlPageLinkRendererBegin( $linkRenderer, $target, &$text,
		&$customAttribs, &$query, &$ret
	) {
		if ( isset( $customAttribs['class'] ) ) {
			$customAttribs['class'] .= ' ';
		} else {
			$customAttribs['class'] = '';
		}

		$customAttribs['class'] .= 'fw-link';
		return true;
	}
}
