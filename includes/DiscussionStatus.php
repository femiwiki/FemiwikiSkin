<?php

namespace MediaWiki\Skins\Femiwiki;

use ExtensionRegistry;
use MediaWiki\Config\Config;
use MediaWiki\ResourceLoader\Context;

class DiscussionStatus {
	/**
	 * Config for skins.femiwiki.js/discussionStatus.js
	 *
	 * @param Context $context
	 * @param Config $config
	 * @return array
	 */
	public static function getConfig( Context $context, Config $config ): array {
		$registry = ExtensionRegistry::getInstance();
		return [
			'useDiscussionTools' => $registry->isLoaded( 'DiscussionTools' ),
			'useFlow' => $registry->isLoaded( 'Flow' ),
			'openTopicDays' => $config->get( 'FemiwikiOpenTopicDays' ),
		];
	}
}
