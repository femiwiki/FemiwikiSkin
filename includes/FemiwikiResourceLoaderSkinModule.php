<?php

class FemiwikiResourceLoaderSkinModule extends \ResourceLoaderSkinModule {
	/**
	 * @param \ResourceLoaderContext $context
	 * @return array
	 */
	protected function getLessVars( \ResourceLoaderContext $context ) {
		$lessVars = parent::getLessVars( $context );
		$logos = $this->getConfig()->get( 'Logos' );

		if ( isset( $logos[ 'svg' ] ) ) {
			$lessVars[ 'symbol-enabled'] = true;
			$lessVars[ 'symbol-url' ] = CSSMin::buildUrlValue( $logos['svg'] );
		} else {
			$lessVars[ 'symbol-enabled'] = false;
		}

		return $lessVars;
	}
}
