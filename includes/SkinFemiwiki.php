<?php
use FemiwikiSkin\Constants;

/**
 * SkinTemplate class for the Femiwiki skin
 *
 * @ingroup Skins
 */
class SkinFemiwiki extends SkinTemplate {
	/** @var string */
	public $skinname = Constants::SKIN_NAME;
	/** @var string */
	public $stylename = 'Femiwiki';
	/** @var string */
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

		// Styles
		$modules['styles'] = array_merge(
			$modules['styles'],
			[
				'skins.femiwiki',
				'skins.femiwiki.xeicon',
				'oojs-ui.styles.icons-interactions'
			]
		);
		$modules[$this->skinname] = array_filter( [
			'skins.femiwiki.js',
			$this->getUser()->isLoggedIn() ? 'skins.femiwiki.notifications' : null,
			!$this->getTitle()->isSpecialPage() ? 'skins.femiwiki.share' : null
		] );

		return $modules;
	}
}
