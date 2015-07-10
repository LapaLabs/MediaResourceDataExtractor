<?php

namespace LapaLabs\YoutubeHelper\Exception;

use LapaLabs\YoutubeHelper\Resource\YoutubeResource;
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
     * @param YoutubeResource $resource
     * @param int $host
     * {@inheritdoc}
     */
    public function __construct(YoutubeResource $resource, $host, $message = "", $code = 0, Exception $previous = null)
    {
        if (!$message) {
            $message = sprintf(
                'Invalid YouTube resource host "%s". The valid hosts are: %s.',
                $host,
                implode(', ', $resource->getValidHosts())
            );
        }

        parent::__construct($message, $code, $previous);
    }
}
