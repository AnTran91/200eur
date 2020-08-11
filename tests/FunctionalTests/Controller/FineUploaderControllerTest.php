<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\FunctionalTests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Fine Uploader Tests
 *
 * As a User After logging in :
 *
 * I can upload one or multiple pictures
 * I can view my uploaded pictures
 * I can delete an pictures
 */
class FineUploaderControllerTest extends WebTestCase
{
    protected $createdFiles;

    /**
     * @var Client
     */
    protected $client;
    protected $container;
    protected $requestHeaders;
    protected $router;
    protected $total = 6;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'SimpleUser',
            'PHP_AUTH_PW'   => 'SimpleUser',
        ));

        $this->container = $this->client->getContainer();
        $this->createdFiles = [];
        $this->requestHeaders = [
            'HTTP_ACCEPT' => 'application/json',
        ];


        $this->router = $this->container
            ->get('router')
        ;
    }

    protected function createTempFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'upl'); // create file
        imagepng(imagecreatetruecolor(100, 100), $file); // create and write image/png to it

        $this->createdFiles = $file;

        return $file;
    }

    protected function getRequestParameters()
    {
        return [
          'qquuid' => 'veryuuid',
          'qqfilename' => 'cat.png',
          'qqtotalfilesize' => 130
        ];
    }

    protected function getChunkRequestParameters()
    {
        return [
          'qquuid' => 'very_chunked_uuid',
          'qqfilename' => 'cat.png',
          'qqtotalfilesize' => 9776069,
          'qqtotalparts' => 5
        ];
    }

    protected function getRequestFile()
    {
        return [
          'qqfile' => new UploadedFile($this->createTempFile(), 'cat.png', 'image/png', 130)
        ];
    }

    public function deleteRequest()
    {
        yield [['retouch' => 1, 'uid' => 'very_chunked_uuid']];
        yield [['retouch' => 1, 'uid' => 'veryuuid']];
    }

    protected function getNextRequestParameters($i)
    {
        return [
            'qqtotalparts' => $this->total,
            'qqpartindex' => $i,
            'qqpartbyteoffset' => 100,
            'qqchunksize' => 100,
            'qquuid' => 'very_chunked_uuid',
            'qqfilename' => 'cat.png',
        ];
    }

    /**
     * As a User After logging in :
     *
     * I can view my uploaded pictures
     */
    public function testUploadedPictureList()
    {
        $this->client->request('GET', $this->router->generate('fine_uploader_index', ['retouch' => '1']));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $array = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEmpty($array);
    }

    /**
     * As a User After logging in :
     *
     * I can upload one or multiple pictures
     */
    public function testSingleUpload()
    {
        // assemble a request
        $client = $this->client;
        $client->request('POST', $this->router->generate('fine_uploader_upload', ['retouch' => 1]), $this->getRequestParameters(), $this->getRequestFile(), $this->requestHeaders);
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');

        $array = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('newUuid', $array);
        $this->assertArrayHasKey('success', $array);
        $this->assertEquals(true, $array['success']);
    }

    /**
     * As a User After logging in :
     *
     * I can upload chunked picture
     *
     * @depend testSingleUpload
     */
    public function testChunkedUpload()
    {
        // assemble a request
        $client = $this->client;
        for ($i = 0; $i < $this->total; ++$i) {
            $client->request('POST', $this->router->generate('fine_uploader_upload', ['retouch' => 1]), $this->getNextRequestParameters($i), $this->getRequestFile(), $this->requestHeaders);
            $response = $client->getResponse();
            $this->assertTrue($response->isSuccessful());
            $this->assertSame($response->headers->get('Content-Type'), 'application/json');
            $array = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertArrayHasKey('uuid', $array);
            $this->assertArrayHasKey('success', $array);
            $this->assertEquals(true, $array['success']);
        }

        $client->request('POST', $this->router->generate('fine_uploader_chunking', ['retouch' => 1]), $this->getChunkRequestParameters(), [], $this->requestHeaders);
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $array = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('newUuid', $array);
        $this->assertArrayHasKey('success', $array);
        $this->assertEquals(true, $array['success']);
    }

    /**
     * As a User After logging in :
     *
     * I can delete an pictures
     *
     * @depend testChunkedUpload
     * @dataProvider deleteRequest
     */
    public function testDeleteUpload($deleteRequest)
    {
        // assemble a request
        $client = $this->client;
        $client->request('DELETE', $this->router->generate('fine_uploader_delete', $deleteRequest), [], [], $this->requestHeaders);
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');

        $array = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('uuid', $array);
        $this->assertArrayHasKey('success', $array);
        $this->assertEquals(true, $array['success']);
    }
}
