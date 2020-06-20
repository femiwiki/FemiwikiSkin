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
			'data-sidebar' => $this->getSidebarData(),
			'data-header' => [
				'html-sitenotice' => $this->get( 'sitenotice', null ),
				'html-newtalk' => $this->get( 'newtalk' ) ?: null,
				'data-above-title-menu' => $this->getAboveTitleMenu(),
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
								// icon is used as a dummy
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
				'data-toolbox' => $this->getPortal( 'page-tb', $this->getToolboxData(), 'toolbox' ),
				'data-actions' => $this->getPortal( 'actions', $this->data['content_navigation']['actions'] ?? null, 'actions' ),
				'page-lastmod-enabled' => isset( $this->data['content_navigation']['views']['history'] )
					&& $this->get( 'lastmod', null ),
				'page-history' => $this->data['content_navigation']['views']['history']['href'] ?? null,
				'page-lastmod' => $this->get( 'lastmod', null ),
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
			'html-trail' => $this->getTrail() . '</body></html>'
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
	 * @return string return a mustache-friendly modified sidebar data includes personal tools
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
	 * @return array
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
	 * @param string $name
	 * @param array $content
	 * @param string|null $msg
	 * @return string html
	 */
	protected function getPortal( $name, $content, $msg = null ) {
		$msg = $this->getMsg( $msg ?: $name );
		$label = $msg->exists() ? $msg->text() : $name;

		if ( is_array( $content ) ) {
			$htmlItems = [];
			foreach ( $content as $key => $val ) {
				$htmlItems[] = $this->makeListItem( $key, $val );
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
			'html-after-portal' => $this->getAfterPortlet( $name ),
		];
	}

	/**
	 * The toolbox includes both page-specific-tools and site-wide-tools, but we
	 * need only page-specific-tools.
	 * @return array
	 */
	public function getToolboxData() {
		$toolbox = parent::getToolbox();

		if ( version_compare( MW_VERSION, '1.35', '<' ) ) {
			if ( ExtensionRegistry::getInstance()->isLoaded( 'Sanctions' )
				&& isset( $this->data['sidebar']['TOOLBOX'] ) ) {
				$toolbox = wfArrayInsertAfter(
					$toolbox,
					[ $this->data['sidebar']['TOOLBOX']['sanctions'] ],
					isset( $toolbox['blockip'] ) ? 'blockip' : 'log'
				);
				unset( $this->data['sidebar']['TOOLBOX']['sanctions'] );
			}
			$toolbox = array_merge(
				$toolbox,
				$this->data['sidebar']['TOOLBOX'] ?? []
			);
		}

		foreach ( [ 'upload', 'specialpages' ] as $special ) {
			if ( isset( $toolbox[$special] ) ) {
				unset( $toolbox[$special] );
			}
		}

		return $toolbox;
	}
}
