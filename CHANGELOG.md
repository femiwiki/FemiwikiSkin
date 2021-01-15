# Changelog

Versions and bullets are arranged chronologically from latest to oldest.

## v1.4.1 (Unreleased)

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
