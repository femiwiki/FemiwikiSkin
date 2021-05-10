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

	/** @var array|mixed */
	private $xeIconMap;

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
		$siteNotice = $skin->getSiteNotice();

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
			'data-sidebar' => $this->getSidebarData(),
			'data-header' => [
				'html-sitenotice' => $siteNotice === '' ? null : $siteNotice,
				'data-indicators' => array_filter( array_map( static function ( $id, $content ) {
					return $id == 'mw-helplink' ? null : [
						'id' => $id,
						'html' => $content,
					];
				}, array_keys( $out->getIndicators() ), $out->getIndicators() ) ),
				'html-newtalk' => $this->get( 'newtalk' ) ?: null,
				'data-above-title-menu' => $this->getAboveTitleMenu(),
				'page-language' => $this->get( 'pageLanguage' ),
				'html-title' => $out->getPageTitle(),
				'html-share-button' => isset( $this->data['articleid'] ) && $this->data['articleid'] != 0 ?
					new \OOUI\ButtonWidget( [
						'id' => 'p-share',
						'classes' => [ 'fw-button' ],
						'infusable' => true,
						'icon' => 'share',
						'title' => $this->getMsg( 'skin-femiwiki-share-tooltip' )->escaped(),
						'framed' => false,
						'invisibleLabel' => true
					] ) : null,
				'html-helplink' => $out->getIndicators()['mw-helplink'] ?? null,
				'msg-page-menu-toggle-tooltip' => $this->getMsg( 'skin-femiwiki-page-menu-tooltip' )->text(),
				'data-toolbox' => $this->getPortal( 'page-tb', $this->getToolboxData(), 'toolbox' ),
				'data-actions' => $this->getPortal( 'actions', $this->data['content_navigation']['actions'] ?? null,
					'actions' ),
				'page-lastmod-enabled' => isset( $this->data['content_navigation']['views']['history'] )
					&& $this->lastModified(),
				'page-history' => $this->data['content_navigation']['views']['history']['href'] ?? null,
				'page-lastmod' => $this->lastModified(),
				'data-views' => $this->getPortal( 'views', $this->data['content_navigation']['views'] )
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
			'data-language' => $this->getPortal( 'lang', $this->data['language_urls'], 'otherlanguages' ),
			'html-footer' => $this->getFooter( 'icononly' ),
			'text-add-this-pub-id' => $this->getAddThisPubId(),
			'html-trail' => $this->getTrail() . '</body></html>',
		] );
	}

	/**
	 * Override BaseTemplate::makeSearchInput() to fill the search form by previously
	 *  searched word.
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

		$realAttrs = array_merge( $realAttrs, Linker::tooltipAndAccesskeyAttribs( 'search' ), $attrs );
		return Html::element( 'input', $realAttrs );
	}

	/**
	 * Remove default alert and notice for SkinFemiwikiHooks::onPersonalUrls()
	 * @inheritDoc
	 * @return array mustache-friendly modified data
	 */
	public function getPersonalTools() {
		$personalTools = parent::getPersonalTools();

		if ( ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			unset( $personalTools['notifications-alert'] );
			unset( $personalTools['notifications-notice'] );
		}

		return $this->getPortal( 'personal', $personalTools, 'personaltools' );
	}

	/**
	 * @return array return a mustache-friendly modified sidebar data includes personal tools
	 */
	private function getSidebarData() {
		$sidebar = $this->getSidebar( [
			'toolbox' => false,
			'languages' => false,
		] );

		$sidebar = array_values( array_map( function ( $portal ) {
			return $this->getPortal( $portal['header'], $portal['content'] );
		}, $sidebar ) );

		$personalTools = $this->getPersonalTools();

		// Add the personal tools to the sidebar
		$sidebar = array_merge( [ $personalTools ], $sidebar );

		return $sidebar;
	}

	/**
	 * @return null|string Either watch or unwatch that is available
	 */
	private function getWatch() {
		$actions = $this->data['content_navigation']['actions'];
		foreach ( [ 'watch', 'unwatch' ] as $mode ) {
			if ( isset( $actions[$mode] ) ) {
				$item = $actions[$mode];
				unset( $this->data['content_navigation']['actions'][$mode] );

				return $item;
			}
		}
		return null;
	}

	/**
	 * @return array|null
	 */
	private function getAboveTitleMenu() {
		$content = [];
		$namespace = $this->data['content_navigation']['namespaces'] ?? null;
		$watch = $this->getWatch();

		if ( $namespace ) {
			$content = $namespace;
		}
		if ( $watch ) {
			$content += [ $watch ];
		}

		if ( !$content ) {
			return null;
		}
		return $this->getPortal( 'above-title-menu',  $content );
	}

	/**
	 * A simplified version of Skin::lastModified() that is a protected function.
	 * This is for a case that $wgMaxCredits is set to a positive number. In the case,
	 * `$this->get( 'lastmod' )` returns nothing.
	 * @return string|null html
	 */
	private function lastModified() {
		$skin = $this->getSkin();
		$out = $skin->getOutput();

		if ( !$out->isArticle() || !$out->isRevisionCurrent() ) {
			return null;
		}

		$page = $skin->getWikiPage();

		$timestamp = $page->getTimestamp();
		if ( $timestamp ) {
			$lang = $skin->getLanguage();
			$d = $lang->date( $page->getTimestamp(), true );
			$t = $lang->time( $page->getTimestamp(), true );
		} else {
			$d = '';
			$t = '';
		}

		return $this->getMsg( 'lastmodifiedat', $d, $t )->parse();
	}

	/**
	 * @param string $name
	 * @param array $content
	 * @param string|null $msg
	 * @return array|null html
	 */
	protected function getPortal( $name, $content, $msg = null ) {
		$msg = $this->getMsg( $msg ?: $name );
		$label = $msg->exists() ? $msg->text() : $name;
		$xeIconMap = $this->getXeIconMap();

		if ( is_array( $content ) ) {
			if ( !count( $content ) ) {
				return null;
			}
			$htmlItems = [];
			foreach ( $content as $key => $val ) {
				$options = [];
				if ( isset( $val['id'] ) && isset( $xeIconMap[$val['id']] ) ) {
					$options = [
						'text-wrapper' => [
							[
								'tag' => 'i',
								'attributes' => [
									'class' => 'xi-' . $xeIconMap[$val['id']]
								]
							],
							[
								'tag' => 'span'
							],
						],
						'link-class' => 'xe-icons',
					];
				}
				// Cast class to string, See https://github.com/femiwiki/femiwiki/issues/213
				if ( isset( $val['links'] ) ) {
					foreach ( $val['links'] as $i => $link ) {
						if ( isset( $link['class'] ) && is_array( $link['class'] ) ) {
							$val['links'][$i]['class'] = implode( " ", $link['class'] );
						}
					}
				}
				$htmlItems[] = $this->makeListItem( $key, $val, $options );
			}
			$htmlItems = implode( "\n", $htmlItems );
		} else {
			$htmlItems = $content;
		}

		return [
			'id' => "p-$name",
			'html-tooltip' => Linker::tooltip( 'p-' . $name ),
			'label' => $label,
			'label-id' => "p-$name-label",
			'html-userlangattributes' => $this->data['userlangattributes'] ?? '',
			'html-items' => $htmlItems,
			'html-after-portal' => $this->getSkin()->getAfterPortlet( $name ),
		];
	}

	/**
	 * @return array|mixed
	 */
	private function getXeIconMap() {
		if ( !$this->xeIconMap ) {
			$map = json_decode(
				$this->getMsg( 'skin-femiwiki-xeicon-map.json' )
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
	 * The toolbox includes both page-specific-tools and site-wide-tools, but we
	 * need only page-specific-tools.
	 * @return array
	 */
	public function getToolboxData() {
		$toolbox = $this->data['sidebar']['TOOLBOX'];

		foreach ( [ 'upload', 'specialpages' ] as $special ) {
			if ( isset( $toolbox[$special] ) ) {
				unset( $toolbox[$special] );
			}
		}

		return $toolbox;
	}

	/** @return string|null */
	private function getAddThisPubId() {
		$config = $this->config->get( 'FemiwikiAddThisId' );
		if ( !$config ) {
			return null;
		}
		if ( is_array( $config ) ) {
			return $config['pub'] ?? null;
		}
		return $config;
	}
}
