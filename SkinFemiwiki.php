<?php

/**
 * SkinTemplate class for the Femiwiki skin
 *
 * @ingroup Skins
 */
class SkinFemiwiki extends SkinTemplate {
	public $skinname = 'femiwiki', $stylename = 'Femiwiki',
		$template = 'FemiwikiTemplate', $useHeadElement = true;

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		global $wgFemiwikiHeadItems;

		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );

		// Twitter card
		$out->addMeta( 'twitter:card', 'summary_large_image' );
		$out->addMeta( 'twitter:site', '@femiwikidotcome' );

		// Favicons
		$out->addHeadItems( $wgFemiwikiHeadItems );

		$out->addModuleStyles( [
			'mediawiki.skinning.interface',
			'mediawiki.skinning.content.externallinks',
			'skins.femiwiki'
		] );

		$modules = [
			'skins.femiwiki.js'
		];
		if ( $out->getUser()->isLoggedIn() ) {
			$modules[] = 'skins.femiwiki.notifications';
		}
		if ( $this->canUseWikiPage() && $this->getWikiPage()->getId() != 0 ) {
			$modules[] = 'skins.femiwiki.share';
		}
		$out->addModules( $modules );

		# Always enable OOUI because OOUI icons are used in FemiwikiTemplate class
		$out->enableOOUI();
	}

	/**
	 * Overrides https://doc.wikimedia.org/mediawiki-core/REL1_31/php/classSkinTemplate.html#a8f0695e80dec37e0c122e31e3141506a
	 * @param OutputPage $out
	 */
	public function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
	}
}
