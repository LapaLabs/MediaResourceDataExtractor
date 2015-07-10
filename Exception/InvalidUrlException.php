<?php

namespace LapaLabs\YoutubeHelper\Exception;

use LapaLabs\YoutubeHelper\Resource\YoutubeResource;
use InvalidArgumentException;
use Exception;

/**
 * Class InvalidUrlException
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */
class InvalidUrlException extends InvalidArgumentException
{
    /**
     * @param YoutubeResource $resource
     * @param int $url
     * {@inheritdoc}
     */
    public function __construct(YoutubeResource $resource, $url, $message = "", $code = 0, Exception $previous = null)
    {
        if (!$message) {
            $message = sprintf(
                'Invalid YouTube resource URL "%s".',
                $url
            );
        }

        parent::__construct($message, $code, $previous);
    }
}
