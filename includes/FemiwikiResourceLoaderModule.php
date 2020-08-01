<?php

class FemiwikiResourceLoaderModule extends \ResourceLoaderSkinModule {
	/**
	 * @param \ResourceLoaderContext $context
	 * @return array
	 */
	protected function getLessVars( \ResourceLoaderContext $context ) {
		$lessVars = parent::getLessVars( $context );
		$logos = $this->getConfig()->get( 'Logos' );

		if ( isset( $logos[ 'icon' ] ) ) {
			$lessVars[ 'logo-icon-enabled'] = true;
			$lessVars[ 'logo-icon-url' ] = CSSMin::buildUrlValue( $logos['icon'] );
		} else {
			$lessVars[ 'logo-icon-enabled'] = false;
		}

		return $lessVars;
	}
}
