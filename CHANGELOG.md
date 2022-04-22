# Changelog

Versions and bullets are arranged chronologically from latest to oldest.

## Unreleased version

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

## Previous Releases

- [REL1_38](https://github.com/femiwiki/FemiwikiSkin/blob/REL1_38/CHANGELOG.md)
- [REL1_37](https://github.com/femiwiki/FemiwikiSkin/blob/REL1_37/CHANGELOG.md)
- [REL1_36](https://github.com/femiwiki/FemiwikiSkin/blob/REL1_36/CHANGELOG.md)
- [REL1_35](https://github.com/femiwiki/FemiwikiSkin/blob/REL1_35/CHANGELOG.md)
