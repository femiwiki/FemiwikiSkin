<?php

/**
 * SkinTemplate class for the Femiwiki skin
 *
 * @ingroup Skins
 */
class SkinFemiwiki extends SkinTemplate {
	public $skinname = 'femiwiki';
	public $stylename = 'Femiwiki';
	public $template = 'FemiwikiTemplate';

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );

		// Twitter card
		$out->addMeta( 'twitter:card', 'summary_large_image' );

		$twitter = $this->getConfig()->get( 'FemiwikiTwitterAccount' );
		if ( $twitter ) {
			$out->addMeta( 'twitter:site', "@$twitter" );
		}

		// Favicons
		$headItems = $this->getConfig()->get( 'FemiwikiHeadItems' );
		if ( $headItems ) {
			$out->addHeadItems( $headItems );
		}

		# Always enable OOUI because OOUI icons are used in FemiwikiTemplate class
		$out->enableOOUI();
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function getDefaultModules() {
		$modules = parent::getDefaultModules();

		$modules['styles'] = array_merge(
			$modules['styles'],
			array_filter( [
				'skins.femiwiki',
				'mediawiki.skinning.content.externallinks',
				'oojs-ui.styles.icons-interactions',
				$this->loggedin ? 'skins.femiwiki.notifications' : null,
				!$this->getTitle()->isSpecialPage() ? 'skins.femiwiki.share' : null
			] )
		);

		$modules[$this->$skinname][] = 'skins.femiwiki.js';

		return $modules;
	}
}
