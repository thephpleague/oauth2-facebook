# CHANGELOG


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
