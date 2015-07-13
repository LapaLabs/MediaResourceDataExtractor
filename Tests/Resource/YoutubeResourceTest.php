<?php

namespace LapaLabs\YoutubeHelper\Resource;

use LapaLabs\YoutubeHelper\Exception\InvalidIdException;

/**
 * Class YoutubeResourceTest
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */
class YoutubeResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validIdProvider
     */
    public function testGetId($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals($id, $resource->getId());
    }

    /**
     * @dataProvider invalidIdProvider
     * @expectedException \Exception
     */
    public function testGetIdException($id)
    {
        $resource = new YoutubeResource($id);
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testToString($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals($id, (string)$resource);
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testCreate($id)
    {
        $resource = YoutubeResource::create($id);

        $this->assertInstanceOf('LapaLabs\YoutubeHelper\Resource\YoutubeResource', $resource);
        $this->assertEquals($id, $resource->getId());
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function testCreateFromUrl($url, $id)
    {
        $resource = YoutubeResource::createFromUrl($url);

        $this->assertInstanceOf('LapaLabs\YoutubeHelper\Resource\YoutubeResource', $resource);
        $this->assertEquals($id, $resource->getId());
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testBuildUrl($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals(sprintf('https://www.youtube.com/watch?v=%s', $id), $resource->buildUrl());
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testBuildDefaultUrl($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals(sprintf('https://www.youtube.com/watch?v=%s', $id), $resource->buildDefaultUrl());
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testBuildAliasUrl($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals(sprintf('https://youtube.com/watch?v=%s', $id), $resource->buildAliasUrl());
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testBuildMobileUrl($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals(sprintf('https://m.youtube.com/watch?v=%s', $id), $resource->buildMobileUrl());
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testBuildShortUrl($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals(sprintf('https://youtu.be/%s', $id), $resource->buildShortUrl());
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testBuildEmbedUrl($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals(sprintf('https://www.youtube.com/embed/%s', $id), $resource->buildEmbedUrl());
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testBuildEmbedCode($id)
    {
        $resource = new YoutubeResource($id);

        $this->assertEquals(
            sprintf(
                '<iframe width="560" height="315" src="https://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>',
                $id
            ),
            $resource->buildEmbedCode()
        );
        $this->assertEquals(
            sprintf(
                '<iframe width="800" height="600" src="https://www.youtube.com/embed/%s" frameborder="0" allowfullscreen class="video"></iframe>',
                $id
            ),
            $resource->buildEmbedCode([
                'class'  => 'video',
                'width'  => 800,
                'height' => 600,
            ])
        );
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testIsValidId($id)
    {
        $this->assertTrue(YoutubeResource::isValidId($id));
    }

    /**
     * @dataProvider validHostProvider
     */
    public function testIsValidHost($host)
    {
        $this->assertTrue(YoutubeResource::isValidHost($host));
    }

    /**
     * @dataProvider validHostProvider
     */
    public function testGetValidHosts($host)
    {
        $this->assertTrue(in_array($host, YoutubeResource::getValidHosts()));
    }

    public function testGetValidHostsCount()
    {
        $this->assertEquals(count($this->validHostProvider()), count(YoutubeResource::getValidHosts()));
    }

    public function testGetDefaultAttributes()
    {
        $resource = new YoutubeResource('5qanlirrRWs');

        $default = [
            'width'           => 560,
            'height'          => 315,
            'src'             => '',
            'frameborder'     => 0,
            'allowfullscreen' => null,
        ];

        $this->assertEquals($default, $resource->getAttributes());
    }

    public function testSetDefaultAttributes()
    {
        $resource = new YoutubeResource('5qanlirrRWs');

        $default = [
            'class'           => 'video',
            'width'           => 800,
            'height'          => 600,
            'src'             => 'http://example.com/any-fail-link',
        ];
        $resource->setAttributes($default);

        $this->assertEquals($default, $resource->getAttributes());
    }

    public function validIdProvider()
    {
        return [
            ['5qanlirrRWs'],
            ['JXMyZ929lpY'],
            ['Mmh-ew1swD4'],
        ];
    }

    public function invalidIdProvider()
    {
        return [
            ['0123456789'],
            ['012345678912'],
//            ['@0123456789'],
//            ['#0123456789'],
//            ['?0123456789'],
//            ['%0123456789'],
//            ['&0123456789'],
//            ['*0123456789'],
//            ['+0123456789'],
//            ['=0123456789'],
        ];
    }

    public function validHostProvider()
    {
        return [
            ['youtu.be'],
            ['youtube.com'],
            ['m.youtube.com'],
            ['www.youtube.com'],
        ];
    }

    public function invalidHostProvider()
    {
        return [
            ['example.com'],
            ['google.com'],
            ['youtube'],
            ['youtube.co'],
            ['youtubecom'],
            ['youtube com'],
        ];
    }

    public function validUrlProvider()
    {
        return [
            ['https://youtube.com/watch?v=5qanlirrRWs',     '5qanlirrRWs'],
            ['https://m.youtube.com/watch?v=5qanlirrRWs',   '5qanlirrRWs'],
            ['https://www.youtube.com/watch?v=5qanlirrRWs', '5qanlirrRWs'],
            ['https://www.youtube.com/embed/5qanlirrRWs',   '5qanlirrRWs'],
            ['https://youtu.be/5qanlirrRWs',                '5qanlirrRWs'],
        ];
    }
}
