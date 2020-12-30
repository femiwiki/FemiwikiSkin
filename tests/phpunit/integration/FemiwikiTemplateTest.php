<?php

namespace FemiwikiSkin\Constants\Tests\Integration;

use FemiwikiTemplate;
use GlobalVarConfig;
use MediaWikiIntegrationTestCase;
use SkinFemiwiki;
use TemplateParser;

/**
 * @group Femiwiki
 */
class FemiwikiTemplateTest extends MediaWikiIntegrationTestCase {

	/**
	 * @return FemiwikiTemplate
	 */
	private function provideFemiwikiTemplateObject() {
		$template = new FemiwikiTemplate(
			GlobalVarConfig::newInstance(),
			new TemplateParser(),
			true
		);
		$template->set( 'skin', new SkinFemiwiki() );
		return $template;
	}

	/**
	 * @covers \FemiwikiTemplate::makeSearchInput
	 */
	public function testMakeSearchInput() {
		$template = $this->provideFemiwikiTemplateObject();
		$arbitraryString = 'lorem ipsum';
		$template->set( 'search', $arbitraryString );
		$rt = $template->makeSearchInput();
		$this->assertStringContainsString( $arbitraryString, $rt,
			'The previously searched word should be displayed' );
	}
}
