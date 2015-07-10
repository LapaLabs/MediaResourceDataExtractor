<?php

namespace LapaLabs\YoutubeHelper\Exception;

use LapaLabs\YoutubeHelper\Resource\YoutubeResource;
use LengthException;
use Exception;

/**
 * Class InvalidIdException
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */
class InvalidIdException extends LengthException
{
    /**
     * @param YoutubeResource $resource
     * {@inheritdoc}
     */
    public function __construct(YoutubeResource $resource, $message = "", $code = 0, Exception $previous = null)
    {
        if (!$message) {
            $message = sprintf(
                'Invalid YouTube resource ID "%s". The length of ID should be equal to 11 characters.',
                $resource->getId()
            );
        }

        parent::__construct($message, $code, $previous);
    }
}
