<?php

/**
 * Test: Url
 */

use Contributte\Http\Url;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(function () {
    $url = new Url('https://github.com');
    Assert::equal('https://github.com/', (string) $url);

    $url->appendPath('foo');
    Assert::equal('https://github.com/foo', (string) $url);
    $url->appendPath('bar');
    Assert::equal('https://github.com/foobar', (string) $url);
});
