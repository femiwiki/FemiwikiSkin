<?php

namespace MediaWiki\Skins\Femiwiki\HookHandler;

// phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
use EchoNotificationController;
use EchoSeenTime;
use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skins\Femiwiki\Constants;
use MediaWiki\Skins\Femiwiki\SkinFemiwiki;
use MWEchoNotifUser;
use Sanitizer;
use SkinTemplate;
use SpecialPage;
use Title;

class Portlet implements
	\MediaWiki\Hook\PersonalUrlsHook,
	\MediaWiki\Hook\SidebarBeforeOutputHook,
	\MediaWiki\Hook\SkinTemplateNavigation__UniversalHook
	{

	/** @var array|mixed */
	private $xeIconMap;

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
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		if ( $sktemplate->getSkinName() !== Constants::SKIN_NAME ) {
			return;
		}

		$this->promoteWatchLinkToView( $sktemplate, $links );
		$this->updatePortletLinksItems( $sktemplate, $links );
	}

	/**
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 */
	private function promoteWatchLinkToView( $sktemplate, &$links ): void {
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

		// Promote watch link from actions to views
		if ( isset( $links['actions']['watch'] ) ) {
			$key = 'watch';
		} elseif ( isset( $links['actions']['unwatch'] ) ) {
			$key = 'unwatch';
		} else {
			return;
		}

		$item = $links['actions'][$key];
		$links['namespaces'][$key] = $item;
		unset( $links['actions'][$key] );
	}

	/**
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 */
	private function updatePortletLinksItems( $sktemplate, &$links ): void {
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
			foreach ( $portlet as $key => &$item ) {
				$this->addIconToListItem( $item, $key );
			}
		}
	}

	/**
	 * @param array &$item
	 * @param string $key
	 */
	private function addIconToListItem( &$item, $key ) {
		if ( isset( $item['links'] ) ) {
			foreach ( $item['links'] as $key2 => &$link ) {
				$this->addIconToListItem( $link, $key2 );
			}
			return;
		}
		$map = $this->getXeIconMap();
		if ( isset( $item['id'] ) && isset( $map['id'][$item['id']] ) ) {
			$icon = $map['id'][$item['id']];
		} elseif ( isset( $item['icon'] ) && isset( $map['icon'][$item['icon']] ) ) {
			$icon = $map['icon'][$item['icon']];
		} elseif ( $key && isset( $map['key'][$key] ) ) {
			$icon = $map['key'][$key];
		} else {
			return;
		}

		if ( isset( $item['links'] ) && isset( $item['class'] ) ) {
			$item['class'] .= ' xi-' . $icon;
		} else {
			$item['link-class'] ??= [];
			$item['link-class'][] = 'xi-' . $icon;
		}
	}

	/**
	 * @return array|mixed
	 */
	private function getXeIconMap() {
		if ( !$this->xeIconMap ) {
			$map = json_decode(
				// TODO: Use class method
				wfMessage( 'skin-femiwiki-xeicon-map.json' )
					->inContentLanguage()
					->plain(),
				true
			);
			foreach ( $map as $k => $v ) {
				$escapedId = Sanitizer::escapeIdForAttribute( $k );
				if ( $k != $escapedId ) {
					$map[$escapedId] = $v;
					unset( $map[$k] );
				}
			}

			$this->xeIconMap = $map;
		}
		return $this->xeIconMap;
	}
}
