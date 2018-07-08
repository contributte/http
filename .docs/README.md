# HTTP

## Content

- [Curl - provides CurlExtension and simple cURL client](#curl)
- [SAPI - provides SapiRequestExtension (fake request in console mode)](#sapi)
- [BasicAuth - simple basic authentication](#basic-authentication)
- Useful classes
    - [URL - extra methods](#url)

## Curl

There is a prepared simple cURL client in this package.

You have to register it at first.

```yaml
extensions:
    curl: Contributte\Http\DI\CurlExtension
```

Extension registers by automatic [`Contributte\Http\Curl\CurlClient`](https://github.com/contributte/http/blob/master/src/Curl/CurlClient.php) as a service.

## SAPI

Every modern PHP application needs sometimes run a few console commands. Let's say sending newsletter campaigns. There is
a tiny problem, there is no request/URL in console/SAPI (Server API) mode. Don't worry, just use our fake request -
`SapiRequestExtension`.

Easies ways is to register extension without any parameters.

```yaml
extensions:
    sapi: Contributte\Http\DI\SapiRequestExtension
```

Otherwise, you can pass directly URL address.

```yaml
extensions:
    sapi: Contributte\Http\DI\SapiRequestExtension(https://contributte.org)
```

List of all options:

```yaml
sapi:
    url: https://contributte.org
    # other params
    query: NULL
    post: NULL
    files: NULL
    cookies: NULL
    headers: NULL
    method: NULL
    remoteAddress: NULL
    remoteHost: NULL
    rawBodyCallback: NULL
```

## Basic Authentication

```yaml
extensions:
    auth: Contributte\Http\DI\BasicAuthExtension
```

You have `enable` this extension by yourself. It's disabled by default.

```yaml
auth:
    enabled: true/false
    title: My security zone
    users:
      username1:
        password: password1
        unsecured: true
      username2:
        password: $2y$10$p.U5q.BuQp02srggig.VDOqj5m7pE1rCwKavVQ3S2TrqWlkqu3qlC
        unsecured: false # secured by default
      username3:
        password: $2y$10$bgievYVQMzsRn5Ysup.NKOVUk66aitAniAmts2EJAa91eqkAhohvC
```

## Useful classes

### Url

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
