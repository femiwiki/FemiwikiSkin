<?php

namespace MediaWiki\Skins\Femiwiki;

use Html;
use Linker;
use MediaWiki\MediaWikiServices;
use OOUI\ButtonWidget;
use OutputPage;
use Sanitizer;
use SkinMustache;
use SpecialPage;

/**
 * Skin subclass for Femiwiki
 *
 * @ingroup Skins
 * @internal
 */
class SkinFemiwiki extends SkinMustache {

	/** @var array|mixed */
	private $xeIconMap;

	/**
	 * @inheritDoc
	 */
	public function getDefaultModules() {
		$user = $this->getUser();
		$registered = $user->isRegistered();
		$config = $this->getConfig();
		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();

		if ( $registered ) {
			$this->options['scripts'][] = 'skins.femiwiki.notifications';
		}
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
		list( $sidebar, $toolbox ) = $this->getSidebar( $parentData['data-portlets-sidebar'] );

		$commonSkinData = array_merge_recursive( $parentData, [
			'data-sidebar' => $sidebar,
			'html-heading-language-attributes' => $this->prepareHeadingLanguageAttributes(),
			'html-share-button' => $this->getShare(),
			'data-toolbox' => $toolbox,
			'html-lastmod' => $this->lastModified(),
			'text-add-this-pub-id' => $this->getAddThisPubId(),
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
	 * Returns divided data for the sidebar and toolbox. 'data-portlets-sidebar' is divided into two
	 * parts, which is useful for Vector, but not useful for other skins.
	 * @return array
	 */
	protected function getSidebar( $portletsSidebar ) {
		$sidebar = [
			$portletsSidebar['data-portlets-first'],
			...$portletsSidebar['array-portlets-rest']
		];
		$ids = array_map( static function( $portlet ) {
			return $portlet['id'];
		}, $sidebar);
		$toolboxId = array_search( 'p-tb', $ids );
		$toolbox = $sidebar[$toolboxId] ?? null;
		unset( $sidebar[$toolboxId] );

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
		return new ButtonWidget( [
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
		$pageLang = $title->getPageViewLanguage();
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

	/**
	 * @return array|mixed
	 */
	private function getXeIconMap() {
		if ( !$this->xeIconMap ) {
			$map = json_decode(
				$this->msg( 'skin-femiwiki-xeicon-map.json' )
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

	/**
	 * @param string $menuName
	 * @param string $itemKey
	 * @return string
	 */
	private static function getIconId( $menuName, $itemKey ): string {
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

	/** @return string|null */
	private function getAddThisPubId() {
		$config = $this->getConfig()->get( Constants::CONFIG_ADD_THIS_ID );
		if ( !$config ) {
			return null;
		}
		if ( is_array( $config ) ) {
			return $config['pub'] ?? null;
		}
		return $config;
	}
}
