# Changelog

Versions and bullets are arranged chronologically from latest to oldest.

## Unreleased version

- Localisations update.

## v5.0.0

- Drop MediaWiki 1.40 support. Now require MediaWiki v1.40 or later is required.

## v4.0.2

- Use data-last-modified text for modifiedat. (https://github.com/femiwiki/FemiwikiSkin/issues/726)

## v4.0.1

- Update dependencies. (https://github.com/femiwiki/FemiwikiSkin/pull/725)

## v4.0.0

- MediaWiki v1.40 or later is required.
- `$wgFemiwikiFacebookAppId` and `$wgFemiwikiTwitterAccount` configuration variables are removed. If you still need to use this feature, please see [Extension:WikiSEO](https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:WikiSEO).
- The default value of `$wgFemiwikiLegacySmallElementsForAnonymousUser` is now `false`.
- Adding icons to menu items using `[[MediaWiki:skin-femiwiki-xeicon-map.json]]` system message is now not supported. Instead you can add styles in `[[MediaWiki:Common.css]]`. Example:

  ```css
  #t-cite a {
    padding-left: 0;
  }
  #t-cite a:before {
    content: '\ea6a';
  }
  ```

## 3.3.0

- AddThis is removed. Instead, Web Share API will be used on supported browsers. (https://github.com/femiwiki/FemiwikiSkin/pull/714)

## 3.2.0

- Make `<hr>` tag visible on localNotice (https://github.com/femiwiki/FemiwikiSkin/pull/700)
- Remove boxes from the framed images (https://github.com/femiwiki/FemiwikiSkin/pull/698)

## 3.1.4

- Indent lower <li>s under 4 level (https://github.com/femiwiki/FemiwikiSkin/pull/677)
- Make headers distinguishable (https://github.com/femiwiki/FemiwikiSkin/pull/679)
- Minify font-size on little larger mobile devices (https://github.com/femiwiki/FemiwikiSkin/pull/681)
- Center VE preview (https://github.com/femiwiki/FemiwikiSkin/pull/685)
- Make FlaggedRevs's comment box not overflow (https://github.com/femiwiki/FemiwikiSkin/pull/687)

## 3.1.3

- Fixed Darkmode. (https://github.com/femiwiki/FemiwikiSkin/issues/653)

## 3.1.2

- Fixed broken DarkMode support. (https://github.com/femiwiki/femiwiki/issues/324)

## 3.1.1

- Fixed missing OOUI theme elements.

## 3.1.0

- Editing in the mobile view is now possible. (https://github.com/femiwiki/FemiwikiSkin/pull/517)
- Some PNG icons are rewritten in SVG format.

## v3.0.0

Breaking changes:

- Femiwiki skin now requires MediaWiki 1.38 or newer.
- `$wgFemiwikiFacebookAppId` and `$wgFemiwikiTwitterAccount` configuration variables are removed. If you still need to use this feature, please see [Extension:WikiSEO](https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:WikiSEO).
- The default value of `$wgFemiwikiLegacySmallElementsForAnonymousUser` is now `false`.
- Adding icons to menu items using `[[MediaWiki:skin-femiwiki-xeicon-map.json]]` system message is now not supported. Instead you can add styles in `[[MediaWiki:Common.css]]`. Example:
  ```css
  #t-cite a {
    padding-left: 0;
  }
  #t-cite a:before {
    content: '\ea6a';
  }
  ```

ENHANCEMENTS:

- Localisations update.

## v2.0.0

- The styles for plain links are disabled by default now. To recover the previous behavior, add `$wgFemiwikiAddLinkClass = true;` to your LocalSettings.php. (https://github.com/femiwiki/FemiwikiSkin/pull/469)
- The elements on the skin are now larger than before by default. Users can disable this on Special:Preferences.
- Fixed the bug that the desktop change confirming dialog is not affected by the wikitext mode in VisualEditor preference on Mobile.
- Fixed the bug of the desktop change confirming dialog on mobile.

## v1.10.10

- Fixed conflict between DarkMode and GrowthExperiments. (https://github.com/femiwiki/FemiwikiSkin/pull/422)

## v1.10.9

- Fixed conflict between DarkMode and RelatedArticles. (https://github.com/femiwiki/FemiwikiSkin/issues/416)

## v1.10.8

- Fixed bugs on the DarkMode extension. (https://github.com/femiwiki/FemiwikiSkin/issues/396, https://github.com/femiwiki/FemiwikiSkin/issues/397)

## v1.10.7

- Fixed FemiwikiUseLargerElements option cannot be disabled after enabling. (https://github.com/femiwiki/FemiwikiSkin/issues/392)

## v1.10.6

- Patched not correctly clipped Echo notification popup on mobile. (https://github.com/femiwiki/FemiwikiSkin/issues/78)
- Fixed the blue spinner on Special:RecentChanges. (https://github.com/femiwiki/FemiwikiSkin/issues/385)
- The label for categories is now in a single line. (https://github.com/femiwiki/FemiwikiSkin/pull/387)

## v1.10.5

- Fixed button colors.

## v1.10.4

- Fixed broken StructuredDiscussions styles. (https://github.com/femiwiki/FemiwikiSkin/issues/365, https://github.com/femiwiki/FemiwikiSkin/issues/366)
- Added a module style for DismissableSiteNotice.

## v1.10.3

- Improved the support of FacetedCategory. (https://github.com/femiwiki/FemiwikiSkin/pull/346)
- Fixed `<pre>` tag overflow problem. (https://github.com/femiwiki/FemiwikiSkin/issues/350)
- Bump OOUI to 0.42.0.

## v1.10.2

- Fixed invalid OOUI icon url. (https://github.com/femiwiki/FemiwikiSkin/issues/315)

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

## v1.5.0

- Define font-family for `[lang=ja]` (https://github.com/femiwiki/FemiwikiSkin/pull/229)

## v1.4.1

- Fix not shortened url on sharing

## v1.4.0

- Add a new configuration variable `$wgFemiwikiAddThisId` for pubid and tool id of [AddThis](https://www.addthis.com/). If it is set, `$wgFemiwikiFacebookAppId` will be ignored.

  ```php
  // Basic usage
  $wgFemiwikiAddThisId = 'xx-xxxxxxxxxxxxxxxx';

  // If you have multiple tools, you must specify the tool id.
  $wgFemiwikiAddThisId = [
    'pub' => 'xx-xxxxxxxxxxxxxxxx',
    'tool' => 'xxxx',
  ];
  ```

## v1.3.5

- Add missing closing label tags

## v1.3.4

- Use `$wgLogos['svg']` if `$wgLogos['icon']` is not set.
- Fix broken Echo icons

## v1.3.3

- Fix the not working markAllRead button

## v1.3.2

BUG FIXES:

- Replace normalize.css with the MediaWiki bundled version.
- Fix PHP Notice: Array to string conversion.
