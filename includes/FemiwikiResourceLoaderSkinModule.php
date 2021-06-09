<?php

namespace MediaWiki\Skins\Femiwiki;

use CSSMin;
use ResourceLoaderContext;
use ResourceLoaderSkinModule;

class FemiwikiResourceLoaderSkinModule extends ResourceLoaderSkinModule {
	/**
	 * @param ResourceLoaderContext $context
	 * @return array
	 */
	protected function getLessVars( ResourceLoaderContext $context ) {
		$lessVars = parent::getLessVars( $context );
		$logos = $this->getConfig()->get( 'Logos' );

		# icon has high priority for FemiwikiSkin's backward compatibility.
		# This behavior will be removed in the next major version up(v2).
		$symbol = $logos[ 'icon' ] ?? $logos[ 'svg' ] ?? null;
		if ( $symbol !== null ) {
			$lessVars[ 'symbol-enabled'] = true;
			$lessVars[ 'symbol-url' ] = CSSMin::buildUrlValue( $symbol );
		} else {
			$lessVars[ 'symbol-enabled'] = false;
		}

		return $lessVars;
	}
}
