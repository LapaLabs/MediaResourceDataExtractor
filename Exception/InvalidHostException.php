<?php

namespace LapaLabs\YoutubeHelper\Exception;

use InvalidArgumentException;
use Exception;

/**
 * Class InvalidHostException
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */
class InvalidHostException extends InvalidArgumentException
{
    /**
     * @param string $host
     * @param array $validHosts
     * {@inheritdoc}
     */
    public function __construct($host, array $validHosts, $message = "", $code = 0, Exception $previous = null)
    {
        if (!$message) {
            $message = sprintf(
                'Invalid YouTube resource host "%s". The valid hosts are: %s.',
                $host,
                implode(', ', $validHosts)
            );
        }

        parent::__construct($message, $code, $previous);
    }
}
