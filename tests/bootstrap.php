<?php

/*
 * This file is part of the Emmobilier v2.0.
 *
 * (c) Sfari Rami <rami2sfari@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    // executes the "php bin/console cache:clear" command
    passthru(sprintf(
        'php "%s/../bin/console" cache:clear --env=%s --no-warmup',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
}
require __DIR__.'/../vendor/autoload.php';
