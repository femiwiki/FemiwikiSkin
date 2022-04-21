<?php
namespace MediaWiki\Skins\Femiwiki;

/**
 * A class for storing constants of FemiwikiSkin.
 * The strings except pref are tightly coupled to the corresponding fields in skin.json.
 *
 * @internal Only for use by Femiwiki skin.
 */
final class Constants {
	/** @var string the key of ValidSkinNames */
	public const SKIN_NAME = 'femiwiki';
	/** @var string the key of ConfigRegistry */
	public const CONFIG_NAME = 'femiwiki';

	/** @var string */
	public const PREF_KEY_LARGER_ELEMENTS = 'FemiwikiUseLargerElements';

	/** @var string */
	public const CONFIG_ADD_LINK_CLASS = 'FemiwikiAddLinkClass';
	/** @var string */
	public const CONFIG_ADD_THIS_ID = 'FemiwikiAddThisId';
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
}
