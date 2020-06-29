<?php
namespace FemiwikiSkin;

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

	/**
	 * @var string
	 */
	public const PREF_KEY_DARK_MODE = 'FemiwikiSkinDarkMode';
}
