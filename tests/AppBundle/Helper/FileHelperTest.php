<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/14/2017
 * Time: 10:53 AM
 */

namespace Tests\AppBundle\Helper;

use AppBundle\Helper\FileHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

class FileHelperTest extends WebTestCase
{
    /** @var  FileHelper */
    protected $fh;
    /** @var  Container */
    protected $serviceContainer;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        //get the DI container
        $this->serviceContainer = $kernel->getContainer();
        $this->fh = $this->serviceContainer->get('app.file_helper');
    }


    public function testExists( )
    {
        $this->assertTrue(isset($this->fh), 'FileHelper is set');
        $this->assertTrue($this->fh instanceof FileHelper, 'FileHelper is a FileHelper');
    }

    function testNormalizePath() {
        $base = $this->serviceContainer->get('kernel')->getRootDir();
        $base = str_replace('\\', '/', $base);
        //Go down and up.
        $path = $this->serviceContainer->get('kernel')->getRootDir().'/web/uploads/../../';
        $result = $this->fh->normalizePath($path);
        $this->assertEquals($base, $result, 'Test normalized path');
    }

    function testGetFileExtension() {
        $fileName = 'tigger.txt';
        $this->assertEquals('txt', $this->fh->getFileExtension($fileName), 'Got file extension');
    }

    function testGetFileNameNoExtension() {
        $fileName = 'tigger.txt';
        $this->assertEquals('tigger', $this->fh->getFileNameWithoutExtension($fileName),
            'Got file name without extension');
    }

    function testGetWebRootFilePath() {
        $expected = $this->fh->normalizePath($this->serviceContainer->get('kernel')->getRootDir().'/../web')
            . '/';
        $actual = $this->fh->getWebRootFilePath();
        $this->assertEquals($expected, $actual, 'Got Web root file path');
    }

    function testGetUploadRootFilePath() {
        $expected = $this->fh->normalizePath($this->serviceContainer->get('kernel')->getRootDir().'/../web')
            . '/' . 'uploads' . '/';
        $actual = $this->fh->getUploadRootFilePath();
        $this->assertEquals($expected, $actual, 'Got upload file path');
    }

    function testRemoveSlashDuplicates() {
        $path = '//this//that////////the/other///';
        $expected = '/this/that/the/other/';
        $this->assertEquals($expected, $this->fh->removeSlashDuplicates($path), 'Removed duplicate /s');
    }

    function testGetUploadRootUri() {
        $expected = '/uploads/';
        $this->assertEquals($expected, $this->fh->getUploadRootUri(), 'Got upload root URI');
    }

    function testAddFinalSlash() {
        $this->assertEquals('dog/', $this->fh->addFinalSlash('dog'), 'Final /');
        $this->assertEquals('dog/', $this->fh->addFinalSlash('dog/'), 'Another final /');
    }

    function testGetUserPhotoUploadDirectory() {
        $p = $this->fh->getWebRootFilePath();
        $expected = $p . 'uploads/user/1/';
        $actual = $this->fh->getUserPhotoUploadDirectory(1);
        $this->assertTrue($expected == $actual,'Got user photo file path');
    }

    function testGetUserPhotoUploadUri() {
        $expected = '/uploads/user/1/';
        $this->assertEquals($expected, $this->fh->getUserPhotoUploadUri(1), 'Got user photo URI');
    }

    function testCreateFilePath() {
        $upRoot = $this->fh->getUploadRootFilePath();
        $dir = $upRoot . 'thing';
        try {
            //Try to create a dir.
            $this->fh->createFilePath($dir);
            $this->assertTrue(file_exists($dir), 'Made a path' );
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
        finally {
            rmdir($dir);
        }
    }

    /**
     * This only tests whether a thumbnail file was created, not what it's content is.
     */
    function testMakeThumbnail() {
        $imagePath = dirname(__FILE__) . '/teagan.jpg';
        $thumbnailPath = dirname(__FILE__) . '/teagan_thumb.jpg';
        try {
            $this->fh->makeThumbnail($imagePath);
            $this->assertTrue(file_exists($thumbnailPath), 'Made a thumbnail');
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
        finally {
            unlink($thumbnailPath);
        }
    }

    function testConvertFileNameToThumbnailname() {
        $imageName = 'teagan.jpg';
        $expectedThumbnailName = 'teagan_thumb.jpg';
        $actualThumbnailName = $this->fh->convertFileNameToThumbnailname($imageName);
        $this->assertEquals($expectedThumbnailName, $actualThumbnailName, 'Computed thumbnail name');
    }

}
