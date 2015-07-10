<?php

namespace LapaLabs\YoutubeHelper\Exception;

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
     * @param string $url
     * {@inheritdoc}
     */
    public function __construct($url, $message = "", $code = 0, Exception $previous = null)
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
