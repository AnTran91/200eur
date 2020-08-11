<?php

/*
 * This file is the core of Symfony.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class CacheKernel extends HttpCache
{
    /*
     * {@inheritDoc}
     */
    protected function getOptions()
    {
        return array(
            'default_ttl' => 0,
            'staleiferror' => 60,
            'stalewhilerevalidate' => 2,
            'allow_revalidate' => true,
            'allow_reload' => true
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function invalidate(Request $request, $catch = false)
    {
        if ('PURGE' !== $request->getMethod()) {
            return parent::invalidate($request, $catch);
        }

        if ('127.0.0.1' !== $request->getClientIp()) {
            return new Response(
                'Invalid HTTP method',
                Response::HTTP_BAD_REQUEST
            );
        }

        $response = new Response();
        if ($this->getStore()->purge($request->getUri())) {
            $response->setStatusCode(Response::HTTP_OK, 'Purged');
        } else {
            $response->setStatusCode(Response::HTTP_NOT_FOUND, 'Not found');
        }

        return $response;
    }
}
