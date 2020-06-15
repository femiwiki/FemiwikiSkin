<?php

/**
 * BaseTemplate class for the Femiwiki skin
 *
 * @ingroup Skins
 */
class FemiwikiTemplate extends BaseTemplate {

	/** @var TemplateParser */
	private $templateParser;
	/** @var string File name of the root (master) template without folder path and extension */
	private $templateRoot = 'skin';

	/**
	 * @param Config|null $config
	 */
	public function __construct( Config $config = null ) {
		parent::__construct( $config );
		$this->templateParser = new TemplateParser( __DIR__ . '/templates' );
	}

	/**
	 * Outputs the entire contents of the page
	 */
	public function execute() {
		$skin = $this->getSkin();
		$out = $skin->getOutput();

		echo $this->templateParser->processTemplate( $this->templateRoot, [
			'html-headelement' => $out->headElement( $skin ),
			'data-navigation-bar' => [
				'msg-navigation-heading' => $this->getMsg( 'navigation-heading' )->parse(),
				'msg-tooltip-n-recentchanges' => $this->getMsg( 'tooltip-n-recentchanges' )->text(),
				'msg-recentchanges-label' => $this->getMsg( 'recentchanges' )->text(),
				'msg-tooltip-n-randompage' => $this->getMsg( 'tooltip-n-randompage' )->text(),
				'msg-randompage-label' => $this->getMsg( 'randompage' )->text(),
				'data-search' => [
					'form-action' => $this->get( 'wgScript' ),
					'html-button-search-fallback' => $this->makeSearchButton(
						'fulltext',
						[ 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' ]
					),
					'html-button-search' => $this->makeSearchButton(
						'go',
						[ 'id' => 'searchButton', 'class' => 'searchButton' ]
					),
					'html-input' => $this->makeSearchInput( [ 'id' => 'searchInput' ] ),
					'msg-search' => $this->getMsg( 'search' ),
					'page-title' => SpecialPage::getTitleFor( 'Search' )->getPrefixedDBkey(),
				]
			],
			'html-user-links' => $this->getUserLinks(),
			'html-sidebar' => $this->getPortals( $this->data['sidebar'] ),
			'data-header' => [
				'html-sitenotice' => $this->get( 'sitenotice', null ),
				'html-newtalk' => $this->get( 'newtalk' ) ?: null,
				'html-namespaces' => $this->getPortlet( [
					'id' => 'p-namespaces',
					'headerMessage' => 'namespaces',
					'content' => $this->data['content_navigation']['namespaces'],
				] ),
				'html-watch' => $this->getWatch(),
				'page-language' => $this->get( 'pageLanguage' ),
				'html-title' => version_compare( MW_VERSION, '1.35', '<' )
					? $this->get( 'title', '' )
					: $out->getPageTitle(),
				'html-title-buttons' => new \OOUI\ButtonGroupWidget(
					[
						'id' => 'p-title-buttons',
						'items' => array_filter( [
							isset( $this->data['articleid'] ) && $this->data['articleid'] != 0 ? new \OOUI\ButtonWidget( [
								'id' => 'p-share',
								'infusable' => true,
								# icon is used as a dummy
								'icon' => 'browser',
								'title' => $this->getMsg( 'skin-femiwiki-share-tooltip' )->escaped(),
								'framed' => false,
								'invisibleLabel' => true
							] ) : null,
							new \OOUI\ButtonWidget( [
								'id' => 'p-menu-toggle',
								'infusable' => true,
								'icon' => 'ellipsis',
								'title' => $this->getMsg( 'skin-femiwiki-page-menu-tooltip' )->escaped(),
								'framed' => false,
								'invisibleLabel' => true
							] )
						] )
					]
				),
				'html-toolbox' => $this->getPortal( 'page-tb', $this->getToolbox(), 'toolbox' ),
				'html-actions' => $this->getPortlet( [
					'id' => 'p-actions',
					'headerMessage' => 'actions',
					'content' => $this->data['content_navigation']['actions'],
				] ),
				'page-history' => $this->data['content_navigation']['views']['history']['href'] ?? null,
				'page-lastmod' => $this->get( 'lastmod', null ),
				'html-views' => $this->getPortlet( [
					'id' => 'p-views',
					'headerMessage' => 'views',
					'content' => $this->data['content_navigation']['views'],
				] )
			],
			'data-content' => [
				// Always returns string, cast to null if empty.
				'html-subtitle' => $this->get( 'subtitle' ) ?: null,
				'html-undelete' => $this->get( 'undelete' ),
				'html-bodycontent' => $this->get( 'bodycontent' ),
				'html-printfooter' => $this->get( 'printfooter' ),
				'html-catlinks' => $this->get( 'catlinks' ),
				'html-data-after-content' => $this->get( 'dataAfterContent' ),
			],
			'html-footer' => $this->getFooterHtml(),
			'html-trail' => $this->getTrail() . '</body></html>'
		] );
	}

	/**
	 * Generates a single sidebar portlet of any kind
	 * @param array $box
	 * @return string html
	 */
	private function getPortlet( $box ) {
		if ( !$box['content'] ) {
			return;
		}

		$html = Html::openElement(
			'div',
			[
				'role' => 'navigation',
				'class' => 'mw-portlet',
				'id' => Sanitizer::escapeId( $box['id'] )
			] + Linker::tooltipAndAccesskeyAttribs( $box['id'] )
		);
		$html .= Html::element(
			'h3',
			[],
			isset( $box['headerMessage'] ) ? $this->getMsg( $box['headerMessage'] )->text() : $box['header'] );
		if ( is_array( $box['content'] ) ) {
			$html .= Html::openElement( 'ul' );
			foreach ( $box['content'] as $key => $item ) {
				$html .= $this->makeListItem( $key, $item );
			}
			$html .= Html::closeElement( 'ul' );
		} else {
			$html .= $box['content'];
		}
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * @return null|string
	 */
	private function getWatch() {
		$nav = $this->data['content_navigation'];
		$mode = $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() )
			? 'unwatch'
			: 'watch';
		if ( isset( $nav['actions'][$mode] ) ) {
			$nav['views'][$mode] = $nav['actions'][$mode];
			$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
			$nav['views'][$mode]['primary'] = true;
			unset( $this->data['content_navigation']['actions'][$mode] );
			$item = $nav['actions'][$mode];
			$attrs = [];
			$attrs['class'] = 'mw-portlet';
			$attrs['id'] = 'ca-watch';

			return Html::rawElement( 'span', $attrs, $this->makeLink( $mode, $item, [] ) );
		}
		return null;
	}

	/**
	 * Override of https://doc.wikimedia.org/mediawiki-core/master/php/classBaseTemplate.html#ad2b95d3e6cd1595ed50a29068374b156
	 * @param array $attrs
	 * @return string
	 */
	public function makeSearchInput( $attrs = [] ) {
		$realAttrs = [
			'type' => 'search',
			'name' => 'search',
			'placeholder' => wfMessage( 'searchsuggest-search' )->text(),
			'value' => $this->get( 'search', '' ),
		];
		// if ( $realAttrs[value]==null ) $realAttrs[value] = str_replace( '_', ' ', $this->get( 'titleprefixeddbkey' ));
		$realAttrs = array_merge( $realAttrs, Linker::tooltipAndAccesskeyAttribs( 'search' ), $attrs );
		return Html::element( 'input', $realAttrs );
	}

	/**
	 * Override of https://doc.wikimedia.org/mediawiki-core/master/php/classBaseTemplate.html#a3148d2373b5ffe603348430207b1042d
	 * @return array
	 */
	public function getToolbox() {
		$toolbox = parent::getToolbox();

		foreach ( [ 'upload', 'specialpages' ] as $special ) {
			if ( isset( $toolbox[$special] ) ) {
				unset( $toolbox[$special] );
			}
		}

		return $toolbox;
	}

	/**
	 * Generates user tools menu
	 * @return string html
	 */
	private function getUserLinks() {
		$personalTools = $this->getPersonalTools();

		// Remove default alert and notice
		if ( ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			unset( $personalTools['notifications-alert'] );
			unset( $personalTools['notifications-notice'] );
		}

		return $this->getPortlet( [
			'id' => 'p-personal',
			'headerMessage' => 'personaltools',
			'content' => $personalTools,
		] );
	}

	/**
	 * Get a series of portals
	 *
	 * @param array $portals
	 * @return string
	 */
	protected function getPortals( $portals ) {
		$html = '';
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$name = (string)$name;

			$html .= $this->getPortal( $name, $content );
		}

		return $html;
	}

	/**
	 * Render a foorter of a page
	 * @return string
	 */
	protected function getFooterHtml() {
		$props = [
			'html-footer-icons' => '',
			'html-language' => $this->getPortal( 'lang', $this->data['language_urls'], 'otherlanguages' ),
			'html-footer-links' => '',
		];

		$props['html-foorter-icons'] = Html::openElement(
			'ul',
			[
				'id' => 'footer-icons',
				'role' => 'contentinfo'
			]
		);
		foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) {
			$props['html-foorter-icons'] .= Html::openElement(
				'li',
				[ 'id' => 'footer-' . Sanitizer::escapeId( $blockName ) . 'ico' ]
			);
			foreach ( $footerIcons as $icon ) {
				$props['html-foorter-icons'] .= $this->getSkin()->makeFooterIcon( $icon );
			}
			$props['html-foorter-icons'] .= Html::closeElement( 'li' );
		}
		$props['html-foorter-icons'] .= Html::closeElement( 'ul' );

		foreach ( $this->getFooterLinks() as $category => $links ) {
			$props['html-footer-links'] .= Html::openElement(
				'ul',
				[
					'id' => 'footer-' . Sanitizer::escapeId( $category ),
					'role' => 'contentinfo'
				]
			);
			foreach ( $links as $key ) {
				if ( $key === 'lastmod' ) { continue;
				}
				$props['html-footer-links'] .= Html::rawElement(
					'li',
					[
						'id' => 'footer-' . Sanitizer::escapeId( $category . '-' . $key )
					],
					$this->get( $key )
				);
			}
			$props['html-footer-links'] .= Html::closeElement( 'ul' );
		}

		return $this->templateParser->processTemplate( 'Footer', $props );
	}

	/**
	 * @param string $name
	 * @param array $content
	 * @param null|string $msg
	 * @return null|string
	 */
	protected function getPortal( $name, $content, $msg = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}

		$msgObj = $this->getMsg( $msg );

		$props = [
			'portal-id' => "p-$name",
			'html-tooltip' => Linker::tooltip( 'p-' . $name ),
			'msg-label' => $msgObj->exists() ? $msgObj->text() : $msg,
			'msg-label-id' => "p-$name-label",
			'html-userlangattributes' => $this->data['userlangattributes'] ?? '',
			'html-portal-content' => '',
			'html-after-portal' => $this->getAfterPortlet( $name ),
		];

		if ( is_array( $content ) ) {
			$props['html-portal-content'] .= '<ul>';
			foreach ( $content as $key => $val ) {
				$props['html-portal-content'] .= $this->makeListItem( $key, $val );
			}
			$props['html-portal-content'] .= '</ul>';
		} else {
			// Allow raw HTML block to be defined by extensions
			$props['html-portal-content'] = $content;
		}

		return $this->templateParser->processTemplate( 'Portal', $props );
	}
}
