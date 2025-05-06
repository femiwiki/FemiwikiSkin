<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use ExtensionRegistry;
use MediaWiki\Extension\Notifications\Controller\NotificationController;
use MediaWiki\Extension\Notifications\NotifUser;
use MediaWiki\Extension\Notifications\SeenTime;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skins\Femiwiki\Constants;
use SkinTemplate;
use SpecialPage;

class Portlet implements
	\MediaWiki\Hook\SidebarBeforeOutputHook,
	\MediaWiki\Hook\SkinTemplateNavigation__UniversalHook
	{

	private const XE_ICON_MAP = [
		'icon' => [
			'die' => 'shuffle',
			'home' => 'home',
			'recentChanges' => 'time',
			'userAvatar' => 'profile',
			'userContributions' => 'list',
			'logIn' => 'log-in',
			'logOut' => 'log-out',
			'settings' => 'cog',
			'userTalk' => 'forum',
			'watchlist' => 'star',
		],
		'id' => [
			'feed-atom' => 'rss-square',
			'feedlinks' => 'rss-square',
			't-blockip' => 'ban',
			't-contributions' => 'list',
			't-info' => 'info',
			't-log' => 'document',
			't-permalink' => 'link',
			't-print' => 'print',
			't-recentchangeslinked' => 'clock-o',
			't-specialpages' => 'library-books',
			't-upload' => 'file-upload',
			't-userrights' => 'group',
			't-whatlinkshere' => 'paper',
		],
		'key' => [
			'delete' => 'trash',
			'move' => 'long-arrow-right',
			'protect' => 'lock',
			'unprotect' => 'unlock',
		],
	];

	/**
	 * Add a single entrypoint "Notifications" item to the user toolbar('personal URLs').
	 * This is an implementation of https://phabricator.wikimedia.org/T299229
	 */
	private function addNotification( SkinTemplate $skin, array &$links ): void {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			return;
		}

		$user = $skin->getUser();
		if ( $user->isAnon() ) {
			return;
		}

		$title = $skin->getTitle();

		$notifUser = NotifUser::newFromUser( $user );
		$count = $notifUser->getNotificationCount();
		$count = max( 0, $count );

		$msgNotificationTimestamp = $notifUser->getLastUnreadMessageTime();
		$alertNotificationTimestamp = $notifUser->getLastUnreadAlertTime();

		$seenAlertTime = SeenTime::newFromUser( $user )->getTime( 'alert', TS_ISO_8601 );
		$seenMsgTime = SeenTime::newFromUser( $user )->getTime( 'message', TS_ISO_8601 );

		$formattedCount = NotificationController::formatNotificationCount( $count );
		$msgText = $skin->msg( 'echo-notification-notice', $count );
		$url = SpecialPage::getTitleFor( 'Notifications' )->getLocalURL();
		$linkClasses = [ "mw-echo-notifications-badge", "mw-echo-notification-badge-fw-nojs" ];

		$unseenMsg = $seenMsgTime !== false && $msgNotificationTimestamp !== false &&
			$seenMsgTime < $msgNotificationTimestamp->getTimestamp( TS_ISO_8601 );
		$unseenAlert = $seenAlertTime !== false && $alertNotificationTimestamp !== false &&
			$seenAlertTime < $alertNotificationTimestamp->getTimestamp( TS_ISO_8601 );
		$hasUnseen = $count > 0 && ( $unseenMsg || $unseenAlert );

		if ( $hasUnseen ) {
			$linkClasses[] = 'mw-echo-unseen-notifications';
		} elseif ( $count === 0 ) {
			$linkClasses[] = 'mw-echo-notifications-badge-all-read';
		}

		if ( $count > NotifUser::MAX_BADGE_COUNT ) {
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

		$links['user-menu'] = wfArrayInsertAfter( $links['user-menu'] ?? [], $insertUrls, 'userpage' );
	}

	/**
	 * Add a "MobileOptions" item to the user toolbar ('personal URLs').
	 */
	private function addMobileOptions( SkinTemplate $skin, array &$links ): void {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' ) ) {
			return;
		}

		$context = MediaWikiServices::getInstance()->getService( 'MobileFrontend.Context' );
		if ( !$context->shouldDisplayMobileView() ) {
			return;
		}

		$title = $skin->getTitle();
		$url = SpecialPage::getTitleFor( 'MobileOptions' )->getLocalURL();

		$insertUrls = [
			'mobile-preferences' => [
				'href' => $url,
				'text' => $skin->msg( 'prefs-mobile' )->text(),
				'active' => ( $url == $title->getLocalUrl() )
			]
		];

		$personalUrls = $links['user-menu'] ?? [];
		if ( $skin->getUser()->isRegistered() && array_key_exists( 'preferences', $personalUrls ) ) {
			$personalUrls = wfArrayInsertAfter( $personalUrls, $insertUrls, 'preferences' );
		} else {
			$personalUrls = array_merge( $personalUrls, $insertUrls );
		}
		$links['user-menu'] = $personalUrls;
	}

	/**
	 * Note that this hook is called by all pages, including special pages.
	 * @inheritDoc
	 * @phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		// phpcs:enable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
		if ( $sktemplate->getSkinName() !== Constants::SKIN_NAME ) {
			return;
		}

		$this->addNotification( $sktemplate, $links );
		$this->addMobileOptions( $sktemplate, $links );
		$this->tweakWatchActions( $sktemplate, $links );

		foreach ( [
			'user-menu',
			'actions',
		] as &$portlet ) {
			foreach ( $links[$portlet] as $key => &$item ) {
				$this->addIconToListItem( $item, $key );
			}
		}
	}

	/**
	 * Modifies the watch actions.
	 */
	public function tweakWatchActions( SkinTemplate $sktemplate, array &$links ): void {
		$title = $sktemplate->getRelevantTitle();
		if ( !$title || !$title->canExist() ) {
			return;
		}

		// Show anonymous users the watch action
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

		// Promote the watch link from actions to views
		if ( isset( $links['actions']['watch'] ) ) {
			$key = 'watch';
		} elseif ( isset( $links['actions']['unwatch'] ) ) {
			$key = 'unwatch';
		} else {
			return;
		}

		$links['namespaces'][$key] = $links['actions'][$key];
		unset( $links['actions'][$key] );
	}

	/**
	 * @inheritDoc
	 */
	public function onSidebarBeforeOutput( $skin, &$sidebar ): void {
		if ( $skin->getSkinName() !== Constants::SKIN_NAME ) {
			return;
		}

		// Removes site-scope tools so that only page-scope tools are shown.
		foreach ( [ 'specialpages', 'upload' ] as $key ) {
			if ( isset( $sidebar['TOOLBOX'][$key] ) ) {
				unset( $sidebar['TOOLBOX'][$key] );
			}
		}

		foreach ( $sidebar as &$portlet ) {
			foreach ( $portlet as $itemKey => &$item ) {
				if ( isset( $item['links'] ) ) {
					foreach ( $item['links'] as $linkKey => &$link ) {
						$this->addIconToListItem( $link, $linkKey, true );
					}
				} else {
					$this->addIconToListItem( $item, $itemKey );
				}
			}
		}
	}

	/**
	 * @param array &$item
	 * @param string $key
	 * @param bool $class Adds icon as 'class' attribute instead of 'link-class' attribute.
	 */
	private function addIconToListItem( &$item, $key, bool $class = false ) {
		$map = self::XE_ICON_MAP;
		if ( isset( $item['id'] ) && isset( $map['id'][$item['id']] ) ) {
			$icon = $map['id'][$item['id']];
		} elseif ( isset( $item['icon'] ) && isset( $map['icon'][$item['icon']] ) ) {
			$icon = $map['icon'][$item['icon']];
		} elseif ( $key && isset( $map['key'][$key] ) ) {
			$icon = $map['key'][$key];
		} else {
			return;
		}

		if ( $class && isset( $item['class'] ) ) {
			$item['class'] .= ' xi-' . $icon;
		} else {
			$item['link-class'] ??= [];
			$item['link-class'][] = 'xi-' . $icon;
		}
	}
}
