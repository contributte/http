# Cache

## Content

- [Curl - provides CurlExtension and simple cURL client](#curl)
- [CliRequest - provides CliRequestExtension, URL in console (SAPI) mode](#cli-request)

## Curl

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

## CliRequest

```yaml
extensions:
    cli: Contributte\Http\CliRequestExtension
```

```yaml
extensions:
    cli: Contributte\Http\CliRequestExtension(https://contributte.org)
```

```yaml
cli:
    url: contributte.org
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
