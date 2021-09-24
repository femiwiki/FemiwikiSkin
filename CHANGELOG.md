# Changelog

Versions and bullets are arranged chronologically from latest to oldest.

## Unreleased

- Fixes invalid OOUI icon url. (https://github.com/femiwiki/FemiwikiSkin/issues/315)

## v1.10.1

- `fw-legacy-small-elements` class is now added to the body element when the feature is enabled.

## v1.10.0

- Added paddings to the syntaxhighlight box. (https://github.com/femiwiki/FemiwikiSkin/issues/288)
- Turned off the interface feature.
- Apply link colors to the preview of 2017 editor. (https://github.com/femiwiki/FemiwikiSkin/issues/290)
- Experimental 'Larger Elements' feature is now on testing.
  - A registered end user can enable the feature in Special:Preference.
  - A new configuration variable is added: `$wgFemiwikiLegacySmallElementsForAnonymousUser`. This is for enabling the larger elements for anonymous user but will soon be deprecated after finishing the test. Set this to false to enable the feature for anonymous user.
- ID for `Mediawiki:Skin-femiwiki-xeicon-map.json` of a portlet item is now always provided. The id of item could be generated in a form of `<PORTLET_NAME>-item-<ITEM_KEY>` when the item does not have one. (https://github.com/femiwiki/FemiwikiSkin/issues/297)

## v1.9.2

- Installed OOUI theme repository via the package manager instead of including the source.

## v1.9.1

- Fixed a blue link on Special:Homepage. (https://github.com/femiwiki/FemiwikiSkin/issues/279)
- Removed the padding of catlinks ul. (https://github.com/femiwiki/FemiwikiSkin/issues/280)
- Fixed the blue link of the term of flow editor.
- Fixed blue links on Special:Search.
- Added styles targeting <a> tag.

## v1.9.0

- Added icon to the ca-user navigation.
- Fixed the incorrect watch counter after visual editing.
- The watch counter shows 0 even in the first now.
- Made sure the buttons changed correctly when hovered or active.
- Overridden default style of #mw-createaccount-join.
- Removed #faa700 that is a color of wikimedia design.
- Fixed bad text wrapping in menu items.
- Deactivated the elements feature. (https://github.com/femiwiki/FemiwikiSkin/issues/20)
- Modified the style of menu items to make them look more like buttons.
- Replaced underline of links with border-bottom (https://github.com/femiwiki/FemiwikiSkin/issues/15)
- Fixed different background color of p-menu-toggle when hovered.
- Enlarged the clickable area of the help link.
- Added a custom class to scoping links and replace style rules.
- Fixed border-bottom of echo notification items.
- Added default icon to links to namespaces.
- Added icon to link to the homepage.
- Fixed the bad parameter name of view-topiclist API call.
- Changed color of unseen echo notifications.
- Fixed blue link of impact module of the homepage.
- Fixed not working alignment hack.
- Hidden empty portlet which is shown when the user is not logged-in.
- Removed underline of watch star on flow board.

## v1.8.0

- Removed the tooltip of the logo. (https://github.com/femiwiki/FemiwikiSkin/issues/267)
- Applied pygments 'autumn' style.
- Made most code blocks full width in mobile devices.
- Fixed not even margins of the footer. (https://github.com/femiwiki/FemiwikiSkin/issues/269)
- Fixed the cropped icon of the help link (https://github.com/femiwiki/FemiwikiSkin/issues/180)
- Made black icons less dark as they are not important than headings.
- Header Improvements:
  - Used icons for namespaces and watch/unwatch navigation.
  - Moved navigation below the first heading.
  - Showed numbers follow the discussion and watch.
  - Showed the watch action to anonymous users to encourage signing-ups.

## v1.7.4

- Fixed blue links in the help panel. (https://github.com/femiwiki/FemiwikiSkin/issues/257)
- Made sure the type of sidebar's $items is an array. (https://github.com/femiwiki/FemiwikiSkin/issues/263)
- Fixed blue links on history view. (https://github.com/femiwiki/FemiwikiSkin/issues/266)

## v1.7.3

- Fixed bug in RelatedArticles. (https://github.com/femiwiki/FemiwikiSkin/issues/258)
- Improved login/createaccount form. (https://github.com/femiwiki/FemiwikiSkin/issues/260)

## v1.7.2

- Made ext-related-articles-card 2-columns.
- Removed unwanted text-decoration of Echo notifications.
- Added more cases for resizing large images.
- Removed padding of `.mwhighlight > pre`. (https://github.com/femiwiki/FemiwikiSkin/issues/245)
- Made postEdit notification centered evenly. (https://github.com/femiwiki/FemiwikiSkin/issues/45)
- Fixed blue link for ULS in Special:Preferences. (https://github.com/femiwiki/FemiwikiSkin/issues/244)
- Fixed blue link to description page in media settings. (https://github.com/femiwiki/FemiwikiSkin/issues/247)
- Improved font-family and hyphens per languages. (https://github.com/femiwiki/femiwiki/issues/274)

## v1.7.1

- Used SkinMustach. (https://github.com/femiwiki/FemiwikiSkin/issues/136)
- Fixed unnecessary delimiter in the footer. (https://github.com/femiwiki/FemiwikiSkin/issues/104)
- Used AutoloadNamespace. (https://github.com/femiwiki/femiwiki/issues/121)
- Replaced hard-coded hrefs in GNB. (https://github.com/femiwiki/FemiwikiSkin/issues/241)
- Made margins of ol same to margins of ul.

## v1.7.0

- Made size of notification text bigger.
- Restored disappeared styles of notification. (https://github.com/femiwiki/FemiwikiSkin/issues/237)
- Centered Visual Editor's preview of save dialog. (https://github.com/femiwiki/FemiwikiSkin/issues/238)

## v1.6.1

- Turned off logo skin feature.

## v1.6.0

Note: this version requires MediaWiki 1.36+. Earlier versions are no longer supported.
If you still use those versions of MediaWiki, please use REL1_35 branch instead of this release.

ENHANCEMENTS:

- Localisation updates from. https://translatewiki.net.
- Defined font-family for `[lang=ja]`. (https://github.com/femiwiki/FemiwikiSkin/issues/228).

BUG FIXES:

- Used content-links feature instead of mediawiki.skinning.content.externallinks.

## Previous Releases

- [REL1_35](https://github.com/femiwiki/FemiwikiSkin/blob/REL1_35/CHANGELOG.md)
