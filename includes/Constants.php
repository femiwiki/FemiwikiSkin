<?php
namespace MediaWiki\Skins\Femiwiki;

/**
 * A class for constants of FemiwikiSkin which are for internal usage only. Do not rely on this file
 * as an API as it may change without warning at any time.
 *
 * This is tightly coupled to the ConfigRegistry field in skin.json.
 */
final class Constants {
	/** @var string */
	public const SKIN_NAME = 'femiwiki';
	/** @var string */
	public const CONFIG_NAME = 'femiwiki';

	/** @var string */
	public const PREF_KEY_LARGER_ELEMENTS = 'FemiwikiUseLargerElements';

	/** @var string */
	public const CONFIG_ADD_LINK_CLASS = 'FemiwikiAddLinkClass';
	/** @var string */
	public const CONFIG_ADD_THIS_ID = 'FemiwikiAddThisId';
	/** @var string */
	public const CONFIG_FACEBOOK_APP_ID = 'FemiwikiFacebookAppId';
	/** @var string */
	public const CONFIG_FIREBASE_KEY = 'FemiwikiFirebaseKey';
	/** @var string */
	public const CONFIG_HEAD_ITEMS = 'FemiwikiHeadItems';
	/** @var string */
	public const CONFIG_KEY_SHOW_FOOTER_ICONS = 'FemiwikiShowFooterIcons';
	/** @var string */
	public const CONFIG_KEY_SMALL_ELEMENTS_FOR_ANONYMOUS_USER = 'FemiwikiLegacySmallElementsForAnonymousUser';
	/** @var string */
	public const CONFIG_KEY_USE_PAGE_LANG_FOR_HEADING = 'FemiwikiUsePageLangForHeading';
	/** @var string */
	public const CONFIG_TWITTER_ACCOUNT = 'FemiwikiTwitterAccount';
}
