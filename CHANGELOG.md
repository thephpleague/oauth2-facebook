# CHANGELOG

## 2.0.1 - July 21, 2017

- Fixed a bug when checking Graph versions past v2.9 (#40)

## 2.0.0 - January 25, 2017

- Added support for `oauth2-client` version 2.x.

## 1.4.4 - October 20, 2016

- Deprecated the `bio` field and `getBio()` method on the User node since it was removed in Graph v2.8. (#27)

## 1.4.3 - October 13, 2016

- Added Graph API version number validation to give a more descriptive error message. (#26)

## 1.4.2 - August 2, 2016

- Added the `age_range` field to the User node.
- Added the `getMaxAge()` and `getMinAge()` methods to `FacebookUser`.

## 1.4.1 - March 28, 2016

- Fixed the unit tests for HHVM.

## 1.4.0 - March 28, 2016

- Now all requests to the Graph API have the [app secret proof](https://developers.facebook.com/docs/graph-api/securing-requests#appsecret_proof) appended to the URL automatically.
- Fixed a bug with `isDefaultPicture()` not recognizing `false`.
- Added list of development files to be ignored when `--prefer-dist` flag is set in Composer for faster installs.

## 1.3.0 - March 17, 2016

- Added `getTimezone()` method to `FacebookUser`.

## 1.2.0 - February 24, 2016

- Added `isDefaultPicture()` method to `FacebookUser`.

## 1.1.0 - September 16, 2015

- Added `getCoverPhotoUrl()` method to `FacebookUser`.

## 1.0.0 - August 20, 2015

- Stable release! Boom!

## 1.0.0 Alpha 2 - August 10, 2015

- Renamed `asArray()` to  `toArray()` on `FacebookUser` to match the interface.

## 1.0.0 Alpha 1 - August 5, 2015

- Tagged an alpha release since we're closer to launch.

## 0.0.12 - July 28, 2015

- Added a method `asArray()` to `FacebookUser` to get all the data from the User node as a plain-old PHP array.

## 0.0.11 - July 14, 2015

- Renamed references from "user" to "resource owner" per [#376](https://github.com/thephpleague/oauth2-client/pull/376).

## 0.0.10 - July 8, 2015

- Fixes for most recent oAuth Client changes before stable release.

## 0.0.9 - June 30, 2015

- Additional fixes for oAuth Client v1.0 alpha 1

## 0.0.8 - June 16, 2015

- Fix for "funny" Facebook `content-type` responses.

## 0.0.7 - June 16, 2015

- Refactored to work with latest 1.0 branch of OAuth 2.0 Client package.
- Added support for exchanging short-lived access tokens with long-lived access tokens.

## 0.0.6 - May 14, 2015

- Refactored to work with latest 1.0 branch of OAuth 2.0 Client package.
- Added support for Facebook's [beta tier](https://developers.facebook.com/docs/apps/beta-tier).

## 0.0.5 - April 21, 2015

- Fixed Graph-specific error response handling.

## 0.0.4 - April 20, 2015

- Updated package to run on `egeloen/http-adapter` instead of Guzzle.

## 0.0.3 - April 20, 2015

- Added support to properly handle the new json response for access tokens starting in Graph `v2.3`.
- If the `graphApiVersion` option is not provided to the `Facebook` provider constructor an `\InvalidArgumentException` will be thrown.
- Removed the `Facebook::DEFAULT_GRAPH_VERSION` fallback value.
- Updated docs to reflect latest Graph version `v2.3`.

## 0.0.2 - February 4, 2015

- Added `branch-alias` to `composer.json`.

## 0.0.1 - February 4, 2015

- Updated `composer.json` to require OAuth 2.0 Client v1.0 with `@dev` flag.
- Updated tests to mock Guzzle v5.x.
- Added test to ensure an exception is thrown when trying to refresh an access token.

## 0.0.0 - February 4, 2015

- Initial release. Hello world!
