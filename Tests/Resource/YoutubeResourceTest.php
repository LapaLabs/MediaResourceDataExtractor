<?php

namespace LapaLabs\YoutubeHelper\Resource;

/**
 * Class YoutubeResourceTest
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */
class YoutubeResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider idProvider
     */
    public function testCreate($id, $expected)
    {
        if (true === $expected) {
            $this->assertInstanceOf('LapaLabs\YoutubeHelper\Resource\YoutubeResource', YoutubeResource::create($id));
        }
    }

    /**
     * @dataProvider idProvider
     */
    public function testIsValidId($id, $expected)
    {
        $this->assertSame($expected, YoutubeResource::isValidId($id));
    }

    /**
     * @dataProvider hostProvider
     */
    public function testIsValidHost($host, $expected)
    {
        $this->assertSame($expected, YoutubeResource::isValidHost($host));
    }

    /**
     * @dataProvider hostProvider
     */
    public function testGetValidHosts($host, $expected)
    {
        if (true === $expected) {
            $this->assertTrue(in_array($host, YoutubeResource::getValidHosts()));
        } else {
            $this->assertFalse(in_array($host, YoutubeResource::getValidHosts()));
        }
    }

    public function testGetValidHostsCount()
    {
        $this->assertEquals(4, count(YoutubeResource::getValidHosts()));
    }

    public function idProvider()
    {
        return [
            ['5qanlirrRWs',     true],
            ['JXMyZ929lpY',     true],
            ['Mmh-ew1swD4',     true],
            ['0123456789',      false],
            ['012345678912',    false],
        ];
    }

    public function hostProvider()
    {
        return [
            ['youtu.be',        true],
            ['youtube.com',     true],
            ['m.youtube.com',   true],
            ['www.youtube.com', true],
            ['example.com',     false],
            ['google.com',      false],
            ['youtube',         false],
            ['youtube.co',      false],
            ['youtubecom',      false],
            ['youtube com',     false],
        ];
    }
}
