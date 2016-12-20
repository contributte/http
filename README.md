# Contributte > HTTP

:sparkles: Extra contribution to [`nette/http`](https://github.com/nette/http).

-----

[![Build Status](https://img.shields.io/travis/contributte/http.svg?style=flat-square)](https://travis-ci.org/contributte/http)
[![Code coverage](https://img.shields.io/coveralls/contributte/http.svg?style=flat-square)](https://coveralls.io/r/contributte/http)
[![Downloads this Month](https://img.shields.io/packagist/dm/contributte/http.svg?style=flat-square)](https://packagist.org/packages/contributte/http)
[![Downloads total](https://img.shields.io/packagist/dt/contributte/http.svg?style=flat-square)](https://packagist.org/packages/contributte/http)
[![Latest stable](https://img.shields.io/packagist/v/contributte/http.svg?style=flat-square)](https://packagist.org/packages/contributte/http)
[![Latest unstable](https://img.shields.io/packagist/vpre/contributte/http.svg?style=flat-square)](https://packagist.org/packages/contributte/http)
[![Licence](https://img.shields.io/packagist/l/contributte/http.svg?style=flat-square)](https://packagist.org/packages/contributte/http)
[![HHVM Status](https://img.shields.io/hhvm/contributte/http.svg?style=flat-square)](http://hhvm.h4cc.de/package/contributte/http)

## Discussion / Help

[![Join the chat](https://img.shields.io/gitter/room/contributte/contributte.svg?style=flat-square)](https://gitter.im/contributte/contributte?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Install

```
composer require contributte/http
```

## Usage

### Curl

There is a prepared simple cURL client in this package.

You have to register it at first.

```yaml
extensions:
    curl: Contributte\Http\CurlExtension
```

Extension registers by automatic [`Contributte\Http\Curl\CurlClient`](https://github.com/contributte/http/blob/master/src/Curl/CurlClient.php) as a service.

### URL

Few methods added:

#### `$url->appendPath($path)`

```php
use Contributte\Http\Url;

$url = new Url('https://github.com');

$url->appendPath('foo');
# https://github.com/foo

$url->appendPath('bar');
# https://github.com/foobar
```

---

Thank you for testing, reporting and contributing.
