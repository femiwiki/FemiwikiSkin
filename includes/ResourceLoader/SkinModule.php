<?php
namespace MediaWiki\Skins\Femiwiki\ResourceLoader;

use MediaWiki\MainConfigNames;
use MediaWiki\ResourceLoader\Context;
use MediaWiki\ResourceLoader\SkinModule as CoreSkinModule;

/**
 * The skin's main style module. It tells the styles which host this wiki is on, so that
 * interwiki-classed links back to this wiki, such as the ones Wikibase draws when this wiki
 * is its own repository, don't get the "leaves this site" globe.
 *
 * @internal Only for use by Femiwiki skin.
 */
class SkinModule extends CoreSkinModule {
	/** @inheritDoc */
	protected function getLessVars( Context $context ) {
		$lessVars = parent::getLessVars( $context );
		$server = (string)$this->getConfig()->get( MainConfigNames::Server );
		$host = parse_url( $server, PHP_URL_HOST );
		$port = parse_url( $server, PHP_URL_PORT );
		if ( is_string( $host ) && $port ) {
			$host .= ':' . $port;
		}
		// Only characters that are safe inside a quoted CSS attribute selector.
		$lessVars['fw-server-host'] = is_string( $host ) && preg_match( '/^[\w.:\[\]-]+$/', $host )
			? '"' . $host . '"'
			: '""';
		return $lessVars;
	}
}
