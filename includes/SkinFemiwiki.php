<?php

namespace MediaWiki\Skins\Femiwiki;

use MediaWiki\MediaWikiServices;
use OOUI\ButtonWidget;
use OutputPage;
use SkinMustache;
use SkinTemplate;
use SpecialPage;

/**
 * Skin subclass for Femiwiki
 *
 * @ingroup Skins
 * @internal
 */
class SkinFemiwiki extends SkinMustache {

	/** @var array|null The history tab, which is listed in the toolbox instead of the views */
	private ?array $historyItem = null;

	private const ACCOUNT_ACTION_IDS = [ 'pt-createaccount', 'pt-login', 'pt-login-private' ];

	/**
	 * @inheritDoc
	 */
	public function getDefaultModules() {
		$user = $this->getUser();
		$registered = $user->isRegistered();
		$config = $this->getConfig();
		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();

		if ( $this->shouldShowShare() ) {
			$this->options['scripts'][] = 'skins.femiwiki.share';
		}
		if (
			( !$registered && $config->get( Constants::CONFIG_KEY_SMALL_ELEMENTS_FOR_ANONYMOUS_USER ) )
			|| ( $registered && !$userOptionsLookup->getBoolOption( $user, Constants::PREF_KEY_LARGER_ELEMENTS ) )
			) {
			$this->options['styles'][] = 'skins.femiwiki.smallElements';
		}
		return parent::getDefaultModules();
	}

	/**
	 * @inheritDoc
	 */
	public function getTemplateData(): array {
		$skin = $this;
		$out = $skin->getOutput();
		$title = $out->getTitle();
		$config = $this->getConfig();
		$parentData = parent::getTemplateData();
		[ $sidebar, $toolbox ] = $this->getSidebar( $parentData['data-portlets-sidebar'] );
		$toolbox = $this->prependHistory( $toolbox );

		$portlets = $parentData['data-portlets'];
		[ $accountActions, $userItems ] = $this->splitAccountActions( array_merge(
			$portlets['data-user-page']['array-items'] ?? [],
			$portlets['data-user-menu']['array-items'] ?? []
		) );
		$extendedUserMenu = [
			'html-items' => $userItems,
		] + $portlets['data-user-menu'];
		if ( $userItems === '' ) {
			$extendedUserMenu['class'] .= ' emptyPortlet';
		}

		$commonSkinData = array_merge_recursive( $parentData, [
			'html-account-actions' => $accountActions,
			'data-user-menu-extended' => $extendedUserMenu,
			'data-sidebar' => $sidebar,
			'html-heading-language-attributes' => $this->prepareHeadingLanguageAttributes(),
			'html-share-button' => $this->getShare(),
			'data-toolbox' => $toolbox,
			'html-lastmod' => $this->extractLastmod( $parentData ),
			'has-footer-icons' => $config->get( Constants::CONFIG_KEY_SHOW_FOOTER_ICONS ),
			'has-indicator' => count( $parentData['array-indicators'] ) !== 0,

			// links
			'link-recentchanges' => SpecialPage::getTitleFor( 'Recentchanges' )->getLocalUrl(),
			'link-random' => SpecialPage::getTitleFor( 'Randompage' )->getLocalUrl(),
			'link-history' => $title->getLocalURL( 'action=history' ),
		] );

		return $commonSkinData;
	}

	/**
	 * Takes the history tab out of the views, where the skin does not show it.
	 * @inheritDoc
	 */
	protected function runOnSkinTemplateNavigationHooks( SkinTemplate $skin, &$content_navigation ) {
		parent::runOnSkinTemplateNavigationHooks( $skin, $content_navigation );
		if ( isset( $content_navigation['views']['history'] ) ) {
			$this->historyItem = $content_navigation['views']['history'];
			unset( $content_navigation['views']['history'] );
		}
	}

	/**
	 * Lists the history tab first in the toolbox, a group every reader sees, so that it does not
	 * open a column of its own.
	 */
	private function prependHistory( ?array $toolbox ): ?array {
		if ( !$toolbox || !$this->historyItem ) {
			return $toolbox;
		}
		// Core sets the id after the hooks, and the id gives the access key and the icon.
		$item = [ 'id' => 'ca-history' ] + $this->historyItem;
		$toolbox['html-items'] = $this->makeListItem( 'history', $item ) . $toolbox['html-items'];
		return $toolbox;
	}

	// Returns the HTML of the sign-up and log-in items, and of the other items
	private function splitAccountActions( array $items ): array {
		$account = '';
		$rest = '';
		foreach ( $items as $item ) {
			if ( in_array( $item['id'] ?? '', self::ACCOUNT_ACTION_IDS, true ) ) {
				$account .= $item['html-item'];
			} else {
				$rest .= $item['html-item'];
			}
		}
		return [ $account, $rest ];
	}

	private function extractLastmod( array $data ): ?string {
		if ( empty( $data['data-last-modified']['text'] ) ) {
			return null;
		}
		return $data['data-last-modified']['text'];
	}

	/**
	 * Returns divided data for the sidebar and toolbox. 'data-portlets-sidebar' is divided into two
	 * parts, which is useful for Vector, but not useful for other skins.
	 */
	protected function getSidebar( array $portletsSidebar ): array {
		$sidebar = [
			$portletsSidebar['data-portlets-first'],
			...$portletsSidebar['array-portlets-rest']
		];
		$ids = array_map( static function ( $portlet ) {
			return $portlet['id'];
		}, $sidebar );
		$toolboxId = array_search( 'p-tb', $ids );
		$toolbox = $sidebar[$toolboxId] ?? null;
		array_splice( $sidebar, $toolboxId, 1 );

		return [ $sidebar, $toolbox ];
	}

	/**
	 * @return bool
	 */
	protected function shouldShowShare() {
		$title = $this->getOutput()->getTitle();
		if ( !$title ) {
			return false;
		}
		return $title->getArticleID() !== 0;
	}

	/**
	 * @return string|null
	 */
	protected function getShare() {
		if ( !$this->shouldShowShare() ) {
			return null;
		}
		return (string)new ButtonWidget( [
			'id' => 'p-share',
			'classes' => [ 'fw-button' ],
			'infusable' => true,
			'icon' => 'share',
			'title' => $this->msg( 'skin-femiwiki-share-tooltip' )->escaped(),
			'framed' => false,
			'invisibleLabel' => true
		] );
	}

	/**
	 * @return string HTML attributes
	 */
	protected function prepareHeadingLanguageAttributes() {
		$config = $this->getConfig();
		if ( !$config->get( Constants::CONFIG_KEY_USE_PAGE_LANG_FOR_HEADING ) ) {
			return parent::prepareUserLanguageAttributes();
		}
		// Use page language for the first heading.
		$title = $this->getOutput()->getTitle();
		$pageLang = $this->getOutput()->getLanguage();
		$pageLangCode = $pageLang->getHtmlCode();
		$pageLangDir = $pageLang->getDir();
		$contLang = MediaWikiServices::getInstance()->getContentLanguage();
		if (
			$pageLangCode !== $contLang->getHtmlCode() ||
			$pageLangDir !== $contLang->getDir()
		) {
			$escPageLang = htmlspecialchars( $pageLangCode );
			$escPageDir = htmlspecialchars( $pageLangDir );
			// Attributes must be in double quotes because htmlspecialchars() doesn't
			// escape single quotes
			return " lang=\"$escPageLang\" dir=\"$escPageDir\"";
		}
		return '';
	}

	private static function getIconId( string $menuName, string $itemKey ): string {
		switch ( $menuName ) {
			case 'user-menu':
				return 'pt-' . $itemKey;
			default:
				return 'ca-' . $itemKey;
		}
	}

	/**
	 * Extends Skin::initPage.
	 *
	 * @inheritDoc
	 */
	public function initPage( OutputPage $out ) {
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );

		// Favicons
		$headItems = $this->getConfig()->get( Constants::CONFIG_HEAD_ITEMS );
		if ( $headItems ) {
			$out->addHeadItems( $headItems );
		}

		# Always enable OOUI because OOUI icons are used in FemiwikiTemplate class
		$out->enableOOUI();
		parent::initPage( $out );
	}
}
