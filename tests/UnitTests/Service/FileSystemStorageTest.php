<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\UnitTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * File System Storage Tests
 */
class FileSystemStorageTest extends WebTestCase
{
    protected $fileSystem;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->fileSystem = static::createClient()
                                    ->getContainer()
                                    ->get('test.file_system_storage');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->fileSystem = null;
    }

    /**
     * set the getDirectory() use for test
     */
    public function getDirectory()
    {
        $container = static::createClient()->getContainer();
        $env = $container->getParameter('kernel.environment');
        $root = $container->getParameter('kernel.project_dir');
        // assemble path
        return sprintf('%s/var/cache/%s/storage', $root, $env);
    }

    /**
     * create temp file for test
     */
    public function getFile()
    {
        // create temporary file
        $file = tempnam(sys_get_temp_dir(), 'uploader');
        $pointer = fopen($file, 'w+');
        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);
        return $file;
    }

    /**
     * Get uploaded File
     *
     * @return UploadedFile
     */
    protected function getRequestFile()
    {
        return new UploadedFile($this->getFile(), 'grumpycat.jpeg', null, null, null, true);
    }


    /**
     * test doUpload method
     */
    public function testDoUpload()
    {
        $this->assertTrue($this->fileSystem->doUpload($this->getRequestFile(), join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'temp', 'fake_uid']), 'grumpycat.jpeg'));
        $filePath = join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'temp', 'fake_uid', 'grumpycat.jpeg']);
        $this->assertFileExists($filePath);
        $this->assertFileIsReadable($filePath);
        $this->assertFileIsWritable($filePath);
    }

    /**
     * test directory manipulation
     */
    public function testDirManipulation()
    {
        $testDir = join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'TestDir']);
        // test doCreateDirIfNotExists
        $this->fileSystem->doCreateDirIfNotExists($testDir);

        // test doCreateDirIfNotExists
        $this->assertTrue($this->fileSystem->doExiste($testDir));

        $this->assertDirectoryExists($testDir);
        $this->assertDirectoryIsReadable($testDir);
        $this->assertDirectoryIsWritable($testDir);

        // test doRemove
        $this->assertTrue($this->fileSystem->doRemove($testDir));

        // test doCreateDirIfNotExists
        $this->assertFalse($this->fileSystem->doExiste($testDir));

        $this->assertDirectoryNotExists($testDir);

        $this->assertDirectoryNotExists($testDir);
    }


    /**
     * chunkedUploadProvider
     *
     * @return array
     */
    public function chunkedUploadProvider()
    {
        yield[ join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'chunked', 'fake_uid']) , 0 , $this->getRequestFile() ];
        yield[ join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'chunked', 'fake_uid']) , 1 , $this->getRequestFile() ];
        yield[ join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'chunked', 'fake_uid']) , 2 , $this->getRequestFile() ];
        yield[ join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'chunked', 'fake_uid']) , 3 , $this->getRequestFile() ];
        yield[ join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'chunked', 'fake_uid']) , 4 , $this->getRequestFile() ];
    }

    /**
     * testChunkedUpload
     *
     * @dataProvider chunkedUploadProvider
     */
    public function testDoChunkedUpload($targetFolder, $index, $chunk)
    {
        $this->assertTrue($this->fileSystem->doChunkedUpload($targetFolder, $index, $chunk));
        $filePath = join(DIRECTORY_SEPARATOR, [$targetFolder, $index]);
        $this->assertFileExists($filePath);
        $this->assertFileIsReadable($filePath);
        $this->assertFileIsWritable($filePath);
    }

    /**
     * test Combine Chunks
     *
     * @depand testDoChunkedUpload()
     */
    public function testDoCombineChunks()
    {
        $file = $this->fileSystem->doCombineChunks(join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'chunked', 'fake_uid']));
        $this->assertInstanceOf(UploadedFile::class, $file);
    }

    /**
     * test Init Files
     *
     * @depand testDoUpload()
     */
    public function testDoInitFiles()
    {
        $filePath = join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'temp']);
        $array = $this->fileSystem->doInitFiles($filePath, 'temp');
        $this->assertCount(1, $array);
        $this->assertArrayHasKey('uuid', $array[0]);
        $this->assertArrayHasKey('thumbnailUrl', $array[0]);
        $this->assertArrayHasKey('name', $array[0]);
        $this->assertEquals('grumpycat.jpeg', $array[0]['name']);
    }

    /**
     * test do Cleanup Chunks
     *
     * @depand testDoCombineChunks()
     */
    public function testDoCleanupChunks()
    {
        $chunksFolder = join(DIRECTORY_SEPARATOR, [$this->getDirectory(), 'chunked', 'fake_uid']);
        $this->fileSystem->doCleanupChunks($chunksFolder);

        $this->assertDirectoryNotExists($chunksFolder);
    }
}
