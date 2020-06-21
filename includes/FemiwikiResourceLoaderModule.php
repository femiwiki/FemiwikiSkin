<?php

class FemiwikiResourceLoaderModule extends \ResourceLoaderSkinModule {
	/**
	 * @param \ResourceLoaderContext $context
	 * @return array
	 */
	protected function getLessVars( \ResourceLoaderContext $context ) {
		$lessVars = parent::getLessVars( $context );
		$logosIsSupported = version_compare( MW_VERSION, '1.35', '>=' );

		$logos = $this->getConfig()->get( $logosIsSupported ? 'Logos' : 'FemiwikiLogos' );

		if ( isset( $logos[ 'icon' ] ) ) {
			$lessVars[ 'logo-icon-enabled'] = true;
			$lessVars[ 'logo-icon-url' ] = CSSMin::buildUrlValue( $logos['icon'] );
		} else {
			$lessVars[ 'logo-icon-enabled'] = false;
		}
		if ( !$logosIsSupported ) {
			if ( isset( $logos['wordmark'] ) ) {
				$logo = $logos['wordmark'];
				$lessVars[ 'logo-enabled' ] = true;
				$lessVars[ 'logo-wordmark-url' ] = CSSMin::buildUrlValue( $logo['src'] );
				$lessVars[ 'logo-wordmark-width' ] = intval( $logo['width'] );
				$lessVars[ 'logo-wordmark-height' ] = intval( $logo['height'] );
			} else {
				$lessVars[ 'logo-enabled' ] = false;
			}
		}

		return $lessVars;
	}
}
