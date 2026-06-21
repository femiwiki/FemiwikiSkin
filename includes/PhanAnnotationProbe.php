<?php

namespace MediaWiki\Skins\Femiwiki;

/**
 * Temporary probe to verify phan annotations. Not for merge.
 */
class PhanAnnotationProbe {

	public function broken(): string {
		return $undefinedProbeVariable;
	}
}
