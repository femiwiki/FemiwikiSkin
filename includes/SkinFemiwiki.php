<?php
use FemiwikiSkin\Constants;

/**
 * SkinTemplate class for the Femiwiki skin
 *
 * @ingroup Skins
 */
class SkinFemiwiki extends SkinTemplate {
	public $skinname = Constants::SKIN_NAME;
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

		if ( $this->getUser()->getOption( Constants::PREF_KEY_DARK_MODE, false ) ) {
			$out->addBodyClasses( [ "skin-{$this->skinname}-darkmode" ] );
		}
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function getDefaultModules() {
		$modules = parent::getDefaultModules();

		// Styles
		$modules['styles'] = array_merge(
			$modules['styles'],
			[
				'skins.femiwiki',
				'mediawiki.skinning.content.externallinks',
				'oojs-ui.styles.icons-interactions'
			]
		);
		$modules[$this->skinname][] = 'skins.femiwiki.js';

		// Scripts
		$modules['core'] = array_merge(
			$modules['core'],
			array_filter( [
				$this->getUser()->isLoggedIn() ? 'skins.femiwiki.notifications' : null,
				!$this->getTitle()->isSpecialPage() ? 'skins.femiwiki.share' : null
			] )
		);

		return $modules;
	}
}
