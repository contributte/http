# Contributte HTTP

## Content

- [Setup](#setup)
- [Curl - simple http client (CurlExtension)](#curl)
- [SAPI - fake request (CliRequestExtension)](#sapi)
- [BasicAuth - simple basic authentication](#basic-authentication)
- [Useful classes](#useful-classes)
    - [Url](#url)

## Setup

```bash
composer require contributte/http
```

## Curl

There is a prepared simple cURL client in this package.

You have to register it at first.

```yaml
extensions:
    curl: Contributte\Http\DI\CurlExtension
```

Extension registers [`Contributte\Http\Curl\CurlClient`](https://github.com/contributte/http/blob/master/src/Curl/CurlClient.php) as a service.

## SAPI

Every modern PHP application needs sometimes to run a few console commands. Let's say sending newsletter campaigns. There is
a tiny problem, there is no request/URL in console/SAPI (Server API) mode. Don't worry, just use our fake request -
`SapiRequestExtension`.

```yaml
extensions:
    sapi: Contributte\Http\DI\SapiRequestExtension
```

List of all options:

```yaml
sapi:
    url: https://contributte.org
    # other params
    query: null
    post: null
    files: null
    cookies: null
    headers: null
    method: null
    remoteAddress: null
    remoteHost: null
    rawBodyCallback: null
```

## Basic Authentication

```yaml
extensions:
    auth: Contributte\Http\DI\BasicAuthExtension
```

You have to `enable` this extension by yourself. It's disabled by default.

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
