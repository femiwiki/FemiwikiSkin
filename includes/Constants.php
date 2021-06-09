<?php
namespace MediaWiki\Skins\Femiwiki;

/**
 * A namespace for FemiwikiSkin constants for internal FemiwikiSkin usage only. **Do not rely on this file as an
 * API as it may change without warning at any time.**
 */
final class Constants {
	/**
	 * This is tightly coupled to the ConfigRegistry field in skin.json.
	 * @var string
	 */
	public const SKIN_NAME = 'femiwiki';

	// These are tightly coupled to skin.json's config.
	/**
	 * @var string
	 */
	public const CONFIG_KEY_USE_PAGE_LANG_FOR_HEADING = 'FemiwikiUsePageLangForHeading';

	/**
	 * @var string
	 */
	public const CONFIG_KEY_SHOW_FOOTER_ICONS = 'FemiwikiShowFooterIcons';
}
