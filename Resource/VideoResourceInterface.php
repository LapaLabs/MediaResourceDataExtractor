<?php

namespace LapaLabs\YoutubeHelper\Resource;

/**
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 */
interface VideoResourceInterface
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public static function isIdValid($id);
}
