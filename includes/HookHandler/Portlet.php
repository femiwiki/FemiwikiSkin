<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use EchoNotificationController;
use EchoSeenTime;
use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skins\Femiwiki\Constants;
use MediaWiki\Skins\Femiwiki\SkinFemiwiki;
use MWEchoNotifUser;
use SkinTemplate;
use SpecialPage;
use Title;

class Portlet implements
	\MediaWiki\Hook\PersonalUrlsHook,
	\MediaWiki\Hook\SkinTemplateNavigationHook
	{

	/**
	 * Handler for PersonalUrls hook.
	 * @inheritDoc
	 */
	public function onPersonalUrls( &$personal_urls, &$title, $skin ): void {
		if ( !$skin instanceof SkinFemiwiki ) {
			return;
		}
		self::addNotification( $personal_urls, $title, $skin );
		self::addMobileOptions( $personal_urls, $title, $skin );
	}

	/**
	 * Add a single entrypoint "Notifications" item to the user toolbar('personal URLs').
	 * This is an implementation of https://phabricator.wikimedia.org/T299229
	 * @param array &$personal_urls Array of URLs to append to.
	 * @param Title &$title Title of page being visited.
	 * @param SkinTemplate $skin
	 * @return void
	 */
	private static function addNotification( &$personal_urls, &$title, $skin ) {
		if ( !$skin instanceof SkinFemiwiki || !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			return;
		}

		$user = $skin->getUser();
		if ( $user->isAnon() ) {
			return;
		}

		$title = $skin->getTitle();

		$notifUser = MWEchoNotifUser::newFromUser( $user );
		$count = $notifUser->getNotificationCount();
		$count = max( 0, $count );

		$msgNotificationTimestamp = $notifUser->getLastUnreadMessageTime();
		$alertNotificationTimestamp = $notifUser->getLastUnreadAlertTime();

		$seenAlertTime = EchoSeenTime::newFromUser( $user )->getTime( 'alert', TS_ISO_8601 );
		$seenMsgTime = EchoSeenTime::newFromUser( $user )->getTime( 'message', TS_ISO_8601 );

		$formattedCount = EchoNotificationController::formatNotificationCount( $count );
		$msgText = $skin->msg( 'echo-notification-notice', $count );
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

		$personal_urls = wfArrayInsertAfter( $personal_urls, $insertUrls, 'userpage' );
	}

	/**
	 * Add a "MobileOptions" item to the user toolbar ('personal URLs').
	 * @param array &$personal_urls Array of URLs to append to.
	 * @param Title &$title Title of page being visited.
	 * @param SkinTemplate $skin
	 */
	private static function addMobileOptions( &$personal_urls, &$title, $skin ) {
		if ( !$skin instanceof SkinFemiwiki || !ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' ) ) {
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
				'text' => $skin->msg( 'prefs-mobile' )->text(),
				'active' => ( $url == $title->getLocalUrl() )
			]
		];

		if ( $skin->getUser()->isRegistered() && array_key_exists( 'preferences', $personal_urls ) ) {
			$personal_urls = wfArrayInsertAfter( $personal_urls, $insertUrls, 'preferences' );
		} else {
			$personal_urls = array_merge( $personal_urls, $insertUrls );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function onSkinTemplateNavigation( $sktemplate, &$links ): void {
		if ( $sktemplate->getSkinName() !== Constants::SKIN_NAME ) {
			return;
		}

		$title = $sktemplate->getRelevantTitle();
		if ( $title && $title->canExist() ) {
			// Show the watch action anonymous users
			if ( !$sktemplate->loggedin ) {
				$links['actions']['watch'] = [
					'class' => 'mw-watchlink-watch',
					'text' => $sktemplate->msg( 'watch' )->text(),
					'href' => $title->getLocalURL( [ 'action' => 'watch' ] ),
					'data' => [
						'mw' => 'interface',
					],
				];
			}

			// Promote watch link from actions to views
			if ( isset( $links['actions']['watch'] ) ) {
				$key = 'watch';
			} elseif ( isset( $links['actions']['unwatch'] ) ) {
				$key = 'unwatch';
			} else {
				return;
			}

			$item = $links['actions'][$key];
			if ( !$item ) {
				return;
			}
			$links['namespaces'][$key] = $item;
			unset( $links['actions'][$key] );
		}
	}
}
