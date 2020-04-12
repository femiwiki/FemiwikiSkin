<?php

/**
 * BaseTemplate class for the Femiwiki skin
 *
 * @ingroup Skins
 */
class FemiwikiTemplate extends BaseTemplate {

	/** @var TemplateParser */
	private $templateParser;

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
		$this->html( 'headelement' );

		echo Html::openElement(
			'div',
			[ 'id' => 'mw-wrapper' ]
		);

		echo $this->templateParser->processTemplate( 'NavigationBar', [
			'navigation-heading' => $this->getMsg( 'navigation-heading' )->parse(),
			'tooltip-n-recentchanges' => $this->getMsg( 'tooltip-n-recentchanges' )->text(),
			'recentchanges-label' => $this->getMsg( 'recentchanges' )->text(),
			'tooltip-n-randompage' => $this->getMsg( 'tooltip-n-randompage' )->text(),
			'randompage-label' => $this->getMsg( 'randompage' )->text(),
			'search' => $this->getSearch()
		] );

		echo Html::openElement(
			'div',
			[ 'id' => 'fw-menu' ]
		);
		// User profile links
		echo $this->getUserLinks();
		echo $this->getPortals( $this->data['sidebar'] );
		echo Html::closeElement( 'div' );

		$this->renderHeader();

		$contentProps = [
			'subtitle' => '',
			'undelete' => $this->get( 'undelete' ),
			'bodycontent' => $this->get( 'bodycontent' ),
			'printfooter' => $this->get( 'printfooter' ),
			'catlinks' => $this->get( 'catlinks' ),
			'data-after-content' => $this->get( 'dataAfterContent' ),
		];

		if ( $this->data['subtitle'] ) {
			$contentProps['subtitle'] = Html::rawelement(
				'p',
				[],
				$this->get( 'subtitle' )
			);
		}

		echo $this->templateParser->processTemplate( 'Content', $contentProps );

		$this->renderFooter();

		echo Html::closeElement( 'div' );

		$this->printTrail();
		echo '</body></html>';
	}

	/**
	 * Generates a single sidebar portlet of any kind
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
	 * Generates the logo and (optionally) site title
	 * @return string html
	 */
	private function getLogo( $id = 'p-logo', $imageOnly = false ) {
		$html = Html::openElement(
			'div',
			[
				'id' => $id,
				'class' => 'mw-portlet',
				'role' => 'banner'
			]
		);
		$html .= Html::element(
			'a',
			[
				'href' => $this->data['nav_urls']['mainpage']['href'],
				'class' => 'mw-wiki-logo',
			] + Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
		);
		if ( !$imageOnly ) {
			$html .= Html::element(
				'a',
				[
					'id' => 'p-banner',
					'class' => 'mw-wiki-title',
					'href' => $this->data['nav_urls']['mainpage']['href']
				] + Linker::tooltipAndAccesskeyAttribs( 'p-logo' ),
				$this->getMsg( 'sitetitle' )->escaped()
			);
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
	 * Generates the search form
	 * @return string html
	 */
	private function getSearch() {
		$html = Html::openElement(
			'form',
			[
				'action' => htmlspecialchars( $this->get( 'wgScript' ) ),
				'role' => 'search',
				'class' => 'mw-portlet',
				'id' => 'p-search'
			]
		);
		$html .= Html::hidden( 'title', htmlspecialchars( $this->get( 'searchtitle' ) ) );
		$html .= Html::rawelement(
			'h3',
			[],
			Html::label( $this->getMsg( 'search' )->escaped(), 'searchInput' )
		);
		$html .= $this->makeSearchInput( [ 'id' => 'searchInput' ] );
		$html .= Html::rawelement(
			'button',
			[
				'id' => 'searchClearButton',
				'type' => 'button'
			],
			'×'
		);
		$html .= $this->makeSearchButton( 'go', [ 'id' => 'searchGoButton', 'class' => 'searchButton' ] );
		$html .= Html::closeElement( 'form' );

		return $html;
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
	 * Render a header of a page
	 * @return string
	 */
	protected function renderHeader() {
		$props = [
			'sitenotice' => '',
			'newtalk' => '',
			'namespaces' => $this->getPortlet( [
				'id' => 'p-namespaces',
				'headerMessage' => 'namespaces',
				'content' => $this->data['content_navigation']['namespaces'],
			] ),
			'watch' => $this->getWatch(),
			'language' => $this->get( 'pageLanguage' ),
			'title' => $this->get( 'title' ),
			'title-buttons' => '',
			'toolbox' => $this->getPortal( 'page-tb', $this->getToolbox(), 'toolbox' ),
			'actions' => $this->getPortlet( [
				'id' => 'p-actions',
				'headerMessage' => 'actions',
				'content' => $this->data['content_navigation']['actions'],
			] ),
			'views' => $this->getPortlet( [
				'id' => 'p-views',
				'headerMessage' => 'views',
				'content' => $this->data['content_navigation']['views'],
			] )
		];

		if ( $this->data['sitenotice'] ) {
			$props['sitenotice'] = Html::rawElement(
				'div',
				[ 'id' => 'siteNotice' ],
				$this->get( 'sitenotice' )
			);
		}
		if ( $this->data['newtalk'] ) {
			$props['newtalk'] = Html::rawElement(
				'div',
				[ 'class' => 'usermessage' ],
				$this->get( 'newtalk' )
			);
		}

		// Make title buttons
		$titleButtons = [];

		if ( isset( $this->data['articleid'] ) && $this->data['articleid'] != 0 ) {
			$titleButtons[] = new \OOUI\ButtonWidget( [
				'id' => 'p-share',
				'infusable' => true,
				# icon is used as a dummy
				'icon' => 'browser',
				'title' => $this->getMsg( 'skin-femiwiki-share-tooltip' )->escaped(),
				'framed' => false,
				'invisibleLabel' => true
			] );
		}
		$titleButtons[] = new \OOUI\ButtonWidget( [
			'id' => 'p-menu-toggle',
			'infusable' => true,
			'icon' => 'ellipsis',
			'title' => $this->getMsg( 'skin-femiwiki-page-menu-tooltip' )->escaped(),
			'framed' => false,
			'invisibleLabel' => true
		] );

		$props['title-buttons'] = new \OOUI\ButtonGroupWidget(
			[
				'id' => 'p-title-buttons',
				'items' => $titleButtons
			]
		);

		if ( isset( $this->data['content_navigation']['views']['history']['href'] ) ) {
			$props['lastmod'] = Html::rawElement(
				'a',
				[
					'id' => 'lastmod',
					'href' => $this->data['content_navigation']['views']['history']['href']
				],
				$this->get( 'lastmod' )
			);
		}

		echo $this->templateParser->processTemplate( 'Header', $props );
	}

	/**
	 * Render a foorter of a page
	 * @return string
	 */
	protected function renderFooter() {
		$props = [
			'footer-icons' => '',
			'language' => $this->getPortal( 'lang', $this->data['language_urls'], 'otherlanguages' ),
			'footer-links' => '',
		];

		$props['foorter-icons'] = Html::openElement(
			'ul',
			[
				'id' => 'footer-icons',
				'role' => 'contentinfo'
			]
		);
		foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) {
			$props['foorter-icons'] .= Html::openElement(
				'li',
				[ 'id' => 'footer-' . Sanitizer::escapeId( $blockName ) . 'ico' ]
			);
			foreach ( $footerIcons as $icon ) {
				$props['foorter-icons'] .= $this->getSkin()->makeFooterIcon( $icon );
			}
			$props['foorter-icons'] .= Html::closeElement( 'li' );
		}
		$props['foorter-icons'] .= Html::closeElement( 'ul' );

		foreach ( $this->getFooterLinks() as $category => $links ) {
			$props['footer-links'] .= Html::openElement(
				'ul',
				[
					'id' => 'footer-' . Sanitizer::escapeId( $category ),
					'role' => 'contentinfo'
				]
			);
			foreach ( $links as $key ) {
				if ( $key === 'lastmod' ) { continue;
				}
				$props['footer-links'] .= Html::rawElement(
					'li',
					[
						'id' => 'footer-' . Sanitizer::escapeId( $category . '-' . $key )
					],
					$this->get( $key )
				);
			}
			$props['footer-links'] .= Html::closeElement( 'ul' );
		}

		echo $this->templateParser->processTemplate( 'Footer', $props );
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
