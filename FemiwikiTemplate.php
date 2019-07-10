<?php

/**
 * BaseTemplate class for the Femiwiki skin
 *
 * @ingroup Skins
 */
class FemiwikiTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
	 */
	public function execute() {
		$this->html( 'headelement' );
		?>

		<div id="mw-wrapper">
			<div class="nav-bar">
				<div id="mw-navigation">
					<h1 id="p-logo">
						<a href="/" class="mw-wiki-logo"><img src="/skins/Femiwiki/images/logo-long.png" alt="Femiwiki"></a>
					</h1>

					<?php
					echo Html::rawElement(
						'h2',
						[],
						$this->getMsg( 'navigation-heading' )->parse()
					);
					?>

					<button id="fw-menu-toggle">
						<span class="icon"></span>
						<span class="badge"></span>
					</button>

					<ul id="site-navigation">
						<li class="changes"><a href="/w/Special:RecentChanges" title="<?php echo $this->getMsg( 'tooltip-n-recentchanges' )->text() ?>"><span class="text"><?php echo $this->getMsg( 'recentchanges' )->text() ?></span></a></li>
						<li class="random"><a href="/w/Special:RandomPage" title="<?php echo $this->getMsg( 'tooltip-n-randompage' )->text() ?>"><span class="text"><?php echo $this->getMsg( 'randompage' )->text() ?></span></a></li>
					</ul>

					<?php
					echo $this->getSearch();
					?>
				</div>
			</div>

			<div id="fw-menu">
				<?php
				// User profile links
				echo $this->getUserLinks();
				$this->renderPortals( $this->data['sidebar'] );
				?>
			</div>

			<?php

			echo Html::openElement(
				'div',
				[ 'id' => 'p-header' ]
			);
				if ( $this->data['sitenotice'] ) {
					echo Html::rawElement(
						'div',
						[ 'id' => 'siteNotice' ],
						$this->get( 'sitenotice' )
					);
				}
				if ( $this->data['newtalk'] ) {
					echo Html::rawElement(
						'div',
						[ 'class' => 'usermessage' ],
						$this->get( 'newtalk' )
					);
				}
				// echo $this->getIndicators();
				echo $this->getPortlet( [
					'id' => 'p-namespaces',
					'headerMessage' => 'namespaces',
					'content' => $this->data['content_navigation']['namespaces'],
				] );
				echo $this->getWatch();

				echo Html::openElement(
					'div',
					[ 'id' => 'p-title-and-tb' ]
				);
				echo Html::rawElement(
					'h1',
					[
						'class' => 'firstHeading',
						'lang' => $this->get( 'pageLanguage' )
					],
					$this->get( 'title' )
				);

				// Make title buttons
				$titleButtons = [];

				if ( isset( $this->data['articleid'] ) && $this->data['articleid'] != 0 ) {
					$titleButtons[] = new OOUI\ButtonWidget( [
						'id' => 'p-share',
						'infusable' => true,
						# icon is used as a dummy
						'icon' => 'browser',
						'title' => $this->getMsg( 'skin-femiwiki-share-tooltip' )->escaped(),
						'framed' => false,
						'invisibleLabel' => true
					] );
				}
				$titleButtons[] = new OOUI\ButtonWidget( [
					'id' => 'p-menu-toggle',
					'infusable' => true,
					'icon' => 'ellipsis',
					'title' => $this->getMsg( 'skin-femiwiki-page-menu-tooltip' )->escaped(),
					'framed' => false,
					'invisibleLabel' => true
				] );

				echo ( new OOUI\ButtonGroupWidget(
					[
						'id' => 'p-title-buttons',
						'items' => $titleButtons
					]
				) );

				echo Html::openElement(
					'div',
					[ 'id' => 'p-actions-and-toolbox' ]
				);
				echo $this->renderPortal( 'page-tb', $this->getToolbox(), 'toolbox' );
				echo $this->getPortlet( [
					'id' => 'p-actions',
					'headerMessage' => 'actions',
					'content' => $this->data['content_navigation']['actions'],
				] );

				echo Html::closeElement( 'div' );
				echo Html::closeElement( 'div' );
				echo Html::openElement(
					'div',
					[ 'id' => 'lastmod-and-views' ]
				);

				if ( isset( $this->data['content_navigation']['views']['history']['href'] ) ) {
					echo Html::rawElement(
						'a',
						[
							'id' => 'lastmod',
							'href' => $this->data['content_navigation']['views']['history']['href']
						],
						$this->get( 'lastmod' )
					);
				}

				echo $this->getPortlet( [
					'id' => 'p-views',
					'headerMessage' => 'views',
					'content' => $this->data['content_navigation']['views'],
				] );

				echo Html::closeElement( 'div' );
				echo Html::closeElement( 'div' );
				?>
			<div id="content" class="mw-body" role="main">
				<div class="mw-body-content" id="bodyContent">
					<?php
					echo Html::openElement(
						'div',
						[ 'id' => 'contentSub' ]
					);
					if ( $this->data['subtitle'] ) {
						echo Html::rawelement(
							'p',
							[],
							$this->get( 'subtitle' )
						);
					}
					echo Html::rawelement(
						'p',
						[],
						$this->get( 'undelete' )
					);
					echo Html::closeElement( 'div' );

					$this->html( 'bodycontent' );
					$this->clear();
					echo Html::rawElement(
						'div',
						[ 'class' => 'printfooter' ],
						$this->get( 'printfooter' )
					);
					$this->html( 'catlinks' );
					$this->html( 'dataAfterContent' );
					?>
				</div>
			</div>
			<hr id="content-end-bar" />

			<div id="mw-footer" class="footer-content">

				<ul id="fw-footer-menu"></ul>
				<?php
				echo Html::openElement(
					'ul',
					[
						'id' => 'footer-icons',
						'role' => 'contentinfo'
					]
				);
				foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) {
					echo Html::openElement(
						'li',
						[
							'id' => 'footer-' . Sanitizer::escapeId( $blockName ) . 'ico'
						]
					);
					foreach ( $footerIcons as $icon ) {
						echo $this->getSkin()->makeFooterIcon( $icon );
					}
					echo Html::closeElement( 'li' );
				}
				echo Html::closeElement( 'ul' );

				echo $this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );

				foreach ( $this->getFooterLinks() as $category => $links ) {
					echo Html::openElement(
						'ul',
						[
							'id' => 'footer-' . Sanitizer::escapeId( $category ),
							'role' => 'contentinfo'
						]
					);
					foreach ( $links as $key ) {
						if ( $key === 'lastmod' ) { continue;
						}
						echo Html::rawElement(
							'li',
							[
								'id' => 'footer-' . Sanitizer::escapeId( $category . '-' . $key )
							],
							$this->get( $key )
						);
					}
					echo Html::closeElement( 'ul' );
				}
				$this->clear();
				?>
			</div>
		</div>

		<?php $this->printTrail() ?>

		</body>
		</html>

		<?php
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
	 * Outputs a css clear using the core visualClear class
	 */
	private function clear() {
		echo '<div class="visualClear"></div>';
	}

	/**
	 * Render a series of portals
	 *
	 * @param array $portals
	 */
	protected function renderPortals( $portals ) {
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$name = (string)$name;

			$this->renderPortal( $name, $content );
		}
	}

	/**
	 * @param string $name
	 * @param array $content
	 * @param null|string $msg
	 */
	protected function renderPortal( $name, $content, $msg = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		$msgObj = wfMessage( $msg );
		$labelId = Sanitizer::escapeId( "p-$name-label" );
		?><div class="portal" role="navigation" id='<?php
		echo Sanitizer::escapeId( "p-$name" )
		?>'<?php
		echo Linker::tooltip( 'p-' . $name )
		?> aria-labelledby='<?php echo $labelId ?>'>
			<h3<?php $this->html( 'userlangattributes' ) ?> id='<?php echo $labelId ?>'><?php
				echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg );
				?></h3>

			<div class="body">
				<?php
				if ( is_array( $content ) ) {
					?>
					<ul>
						<?php
						foreach ( $content as $key => $val ) {
							echo $this->makeListItem( $key, $val );
						}
						?>
					</ul>
					<?php
				} else {
					# Allow raw HTML block to be defined by extensions
					echo $content;
				}

				$this->renderAfterPortlet( $name );
				?>
			</div>
		</div><?php
	}
}
