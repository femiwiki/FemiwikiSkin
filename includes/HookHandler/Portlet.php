<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

use ExtensionRegistry;
use MediaWiki\Extension\Notifications\Controller\NotificationController;
use MediaWiki\Extension\Notifications\NotifUser;
use MediaWiki\Extension\Notifications\SeenTime;
use MediaWiki\Html\Html;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skins\Femiwiki\Constants;
use MediaWiki\Title\Title;
use SkinTemplate;
use SpecialPage;

class Portlet implements
	\MediaWiki\Hook\SidebarBeforeOutputHook,
	\MediaWiki\Hook\SkinTemplateNavigation__UniversalHook
	{

	/**
	 * Codex icons for menu items, by the item's `id`, its `icon` or its key.
	 * Every icon here must also be listed in the skins.femiwiki.icons module.
	 */
	private const ICON_MAP = [
		'icon' => [
			'die' => 'die',
			'home' => 'home',
			'recentChanges' => 'recentChanges',
			'userAdd' => 'userAdd',
			'userAvatar' => 'userAvatar',
			'userContributions' => 'userContributions',
			'logIn' => 'logIn',
			'logOut' => 'logOut',
			'settings' => 'settings',
			'userTalk' => 'userTalk',
			'userTemporary' => 'userTemporary',
			'watchlist' => 'watchlist',
		],
		'id' => [
			'ca-history' => 'history',
			'feed-atom' => 'newspaper',
			'feedlinks' => 'newspaper',
			'pt-userpage' => 'userAvatar',
			't-blockip' => 'block',
			't-contributions' => 'userContributions',
			't-emailuser' => 'message',
			't-info' => 'info',
			't-log' => 'listBullet',
			't-mute' => 'volumeOff',
			't-permalink' => 'link',
			't-print' => 'printer',
			't-recentchangeslinked' => 'clock',
			't-specialpages' => 'specialPages',
			't-upload' => 'upload',
			't-userrights' => 'userRights',
			't-whatlinkshere' => 'articles',
		],
		'key' => [
			'delete' => 'trash',
			'mobile-preferences' => 'settings',
			'move' => 'move',
			'protect' => 'lock',
			'unprotect' => 'unLock',
		],
	];

	/**
	 * Icons for the tabs of related special pages, by the page's canonical name and subpage.
	 * interface.less draws each as `.fw-tab-<icon>`.
	 */
	private const SPECIAL_TAB_ICONS = [
		'Watchlist' => 'watchlist',
		'EditWatchlist' => 'edit',
		'EditWatchlist/raw' => 'wikiText',
		'EditWatchlist/clear' => 'trash',
		'WatchlistLabels' => 'tag',
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
		$msgText = $skin->msg( 'echo-notification-all', $count );
		$url = SpecialPage::getTitleFor( 'Notifications' )->getLocalURL();
		$linkClasses = [ "mw-echo-notifications-badge" ];

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

		$notificationLink = [
			'notifications-all' => [
				'href' => $url,
				'text' => $msgText,
				'active' => ( $url == $title->getLocalUrl() ),
				'class' => $linkClasses,
				'data' => [
					'counter-num' => $count,
					'counter-text' => $formattedCount,
					// ext.echo.init picks up badges with this, for its flyout and its unread-count polling
					'mw-notifications-section' => 'all',
					'badge-icon' => 'bell',
				],
			]
		];

		$links['user-menu'] = $notificationLink + ( $links['user-menu'] ?? [] );
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
		$this->addSpecialTabIcons( $sktemplate, $links );

		foreach ( [
			'user-page',
			'user-menu',
			'actions',
		] as &$portlet ) {
			foreach ( $links[$portlet] as $key => &$item ) {
				self::addIconToListItem( $item, $key );
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

		$links['associated-pages'][$key] = $links['actions'][$key];
		unset( $links['actions'][$key] );
	}

	/**
	 * Marks the tabs core adds for related special pages with their icon. Core numbers them by
	 * position, in the order of SpecialPage::getAssociatedNavigationLinks().
	 */
	private function addSpecialTabIcons( SkinTemplate $sktemplate, array &$links ): void {
		$title = $sktemplate->getTitle();
		if ( !$title || !$title->isSpecialPage() ) {
			return;
		}

		$factory = MediaWikiServices::getInstance()->getSpecialPageFactory();
		$special = $factory->getPage( $title->getText() );
		if ( $special === null ) {
			return;
		}
		$special->setContext( $sktemplate );

		foreach ( $special->getAssociatedNavigationLinks() as $i => $relatedTitleText ) {
			$key = 'special-specialAssociatedNavigationLinks-link-' . $i;
			$relatedTitle = Title::newFromText( $relatedTitleText );
			if ( !isset( $links['associated-pages'][$key] ) || !$relatedTitle ) {
				continue;
			}
			[ $name, $subpage ] = $factory->resolveAlias( $relatedTitle->getDBkey() );
			$icon = self::SPECIAL_TAB_ICONS[$subpage === null ? $name : "$name/$subpage"] ?? null;
			if ( $icon === null ) {
				continue;
			}
			$item = &$links['associated-pages'][$key];
			$item['class'] = trim( ( $item['class'] ?? '' ) . ' fw-tab-' . $icon );
		}
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
						self::addIconToListItem( $link, $linkKey );
					}
				} else {
					self::addIconToListItem( $item, $itemKey );
				}
			}
		}
	}

	/**
	 * Prepends a Codex icon to the link, as Vector 2022 does.
	 * @param array &$item
	 * @param string $key
	 */
	public static function addIconToListItem( &$item, $key ) {
		$map = self::ICON_MAP;
		$icon = $map['id'][$item['id'] ?? ''] ?? $map['icon'][$item['icon'] ?? ''] ?? $map['key'][$key] ?? null;
		if ( $icon === null ) {
			return;
		}

		$item['link-html'] = Html::element( 'span', [
			'class' => [ 'fw-icon', 'mw-ui-icon-wikimedia-' . $icon ],
			'aria-hidden' => 'true',
		] );
	}
}
