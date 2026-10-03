<?php

namespace MediaWiki\Skins\Femiwiki\Tests\Integration;

use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\Request\FauxRequest;
use MediaWiki\ResourceLoader\Context;
use MediaWikiIntegrationTestCase;

/**
 * Builds every module the skin adds skinStyles to, for this skin. A skinStyles
 * file importing a LESS file that core or an extension removed builds with an
 * error that load.php answers with 200 and a comment, and ResourcesTest only
 * checks that the listed files exist. See #1008.
 *
 * @coversNothing
 * @group Database
 */
class SkinStylesTest extends MediaWikiIntegrationTestCase {

	public function testSkinStylesBuild(): void {
		$rl = $this->getServiceContainer()->getResourceLoader();
		$skinStyles = ExtensionRegistry::getInstance()
			->getAttribute( 'ResourceModuleSkinStyles' )['femiwiki'];

		$failed = [];
		$built = 0;
		foreach ( array_keys( $skinStyles ) as $key ) {
			// '+name' adds to the module's own styles rather than replacing them
			$name = ltrim( $key, '+' );
			// A module of an extension this run has not loaded
			if ( !$rl->isModuleRegistered( $name ) ) {
				continue;
			}
			$before = count( $rl->getErrors() );
			$context = new Context( $rl, new FauxRequest( [
				'modules' => $name,
				'only' => 'styles',
				'skin' => 'femiwiki',
			] ) );
			$rl->makeModuleResponse( $context, [ $name => $rl->getModule( $name ) ] );
			foreach ( array_slice( $rl->getErrors(), $before ) as $error ) {
				// The first line names the exception; the rest is its backtrace
				$failed[] = "$name: " . strtok( $error, "\n" );
			}
			$built++;
		}

		$this->assertGreaterThan( 0, $built, 'No module the skin styles is registered' );
		$this->assertSame( [], $failed );
	}
}
