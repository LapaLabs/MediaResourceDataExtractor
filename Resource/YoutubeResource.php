<?php

namespace LapaLabs\YoutubeHelper\Resource;

use LapaLabs\YoutubeHelper\Exception\InvalidHostException;
use LapaLabs\YoutubeHelper\Exception\InvalidIdException;
use LapaLabs\YoutubeHelper\Exception\InvalidUrlException;

/**
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 */
class YoutubeResource extends VideoResource
{
    const YOUTU_BE        = 'youtu.be';
    const YOUTUBE_COM     = 'youtube.com';
    const M_YOUTUBE_COM   = 'm.youtube.com';
    const WWW_YOUTUBE_COM = 'www.youtube.com';
    const HOST_DEFAULT    = self::WWW_YOUTUBE_COM;
    const HOST_ALIAS      = self::YOUTUBE_COM;
    const HOST_MOBILE     = self::M_YOUTUBE_COM;
    const HOST_SHORT      = self::YOUTU_BE;

    protected static $validHosts = [
        self::YOUTU_BE,
        self::YOUTUBE_COM,
        self::M_YOUTUBE_COM,
        self::WWW_YOUTUBE_COM,
    ];

    protected $attributes = [
        'width'           => 560,
        'height'          => 315,
        'src'             => '', // hold position for specific order
        'frameborder'     => 0,
        'allowfullscreen' => null,
    ];

    /**
     * @param $resourceId
     * @return static
     * @throws InvalidIdException
     */
    public static function create($resourceId)
    {
        return new static($resourceId);
    }

    /**
     * @param $resourceUrl
     * @return static
     * @throws InvalidUrlException
     * @throws InvalidHostException
     * @throws InvalidIdException
     */
    public static function createFromUrl($resourceUrl)
    {
        if (false === filter_var($resourceUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidUrlException($resourceUrl);
        }

        $parsedUrl = parse_url($resourceUrl);
        if (isset($parsedUrl['host'])) {
            $host = strtolower($parsedUrl['host']);
            switch ($host) {
                // https://youtube.com/watch?v=5qanlirrRWs
                // https://m.youtube.com/watch?v=5qanlirrRWs
                // https://www.youtube.com/watch?v=5qanlirrRWs
                // https://www.youtube.com/embed/5qanlirrRWs
                case static::YOUTUBE_COM:
                case static::M_YOUTUBE_COM:
                case static::WWW_YOUTUBE_COM:
                    if (true
                        && true === isset($parsedUrl['path'])
                        && true === isset($parsedUrl['query'])
                        && 0 === strcmp('/watch', $parsedUrl['path'])
                        && null === parse_str($parsedUrl['query'], $output)
                        && isset($output['v'])
                        && static::isIdValid($output['v'])
                    ) {
                        $resourceId = $output['v'];
                    } elseif (true
                        && true === isset($parsedUrl['path'])
                        && 1 === preg_match('@^/embed/(?<v>[\w-]{11})($|/|#|\?)@', $parsedUrl['path'], $matches)
                    ) {
                        $resourceId = $matches['v'];
                    } else {
                        throw new InvalidUrlException($resourceUrl);
                    }

                    break;

                // https://youtu.be/5qanlirrRWs
                case static::YOUTU_BE:
                    if (true
                        && true === isset($parsedUrl['path'])
                        && 1 === preg_match('@^/(?<v>[\w-]{11})($|/|#|\?)@', $parsedUrl['path'], $matches)
                    ) {
                        $resourceId = $matches['v'];
                    } else {
                        throw new InvalidUrlException($resourceUrl);
                    }

                    break;

                default:
                    throw new InvalidHostException($host, static::getValidHosts());
            }
        } else {
            throw new InvalidUrlException($resourceUrl);
        }

        return new static($resourceId);
    }

    /**
     * {@inheritdoc}
     */
    public static function isIdValid($id)
    {
        return 11 === strlen($id);
    }

    /**
     * @param string $host
     * @return string
     * @throws InvalidHostException
     */
    protected function buildUrlForHost($host)
    {
        switch (strtolower($host)) {
            // https://youtube.com/watch?v=5qanlirrRWs
            // https://m.youtube.com/watch?v=5qanlirrRWs
            // https://www.youtube.com/watch?v=5qanlirrRWs
            case static::YOUTUBE_COM:
            case static::M_YOUTUBE_COM:
            case static::WWW_YOUTUBE_COM:
                $path = '/watch?v=';
            break;

            // https://youtu.be/5qanlirrRWs
            case static::YOUTU_BE:
                $path = '/';
            break;

            default:
                throw new InvalidHostException($host, static::getValidHosts());
        }

        return 'https://'.$host.$path.$this->id;
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return $this->buildDefaultUrl();
    }

    /**
     * @return string
     */
    public function buildDefaultUrl()
    {
        // https://www.youtube.com/watch?v=5qanlirrRWs
        return $this->buildUrlForHost(static::HOST_DEFAULT);
    }

    /**
     * @return string
     */
    public function buildAliasUrl()
    {
        // https://youtube.com/watch?v=5qanlirrRWs
        return $this->buildUrlForHost(static::HOST_ALIAS);
    }

    /**
     * @return string
     */
    public function buildMobileUrl()
    {
        // https://m.youtube.com/watch?v=5qanlirrRWs
        return $this->buildUrlForHost(static::HOST_MOBILE);
    }

    /**
     * @return string
     */
    public function buildShortUrl()
    {
        // https://youtu.be/5qanlirrRWs
        return $this->buildUrlForHost(static::HOST_SHORT);
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function buildEmbedUrl(array $parameters = [])
    {
        $parameters = array_merge($this->parameters, $parameters);

        // https://www.youtube.com/embed/5qanlirrRWs
        $url = 'https://'.static::HOST_DEFAULT.'/embed/'.$this->id;
        if (count($parameters)) {
            $parameterStrings = [];
            foreach ($parameters as $name => $value) {
                $parameterString = urlencode($name);
                if (null !== $value) {
                    $parameterString .= '='.urlencode($value);
                }
                $parameterStrings[] = $parameterString;
            }
            $url .= '?'.implode('&amp;', $parameterStrings);
        }

        return $url;
    }

    /**
     * Build the valid HTML code to embed this resource
     *
     * @param array $attributes
     * @param array $parameters
     *
     * @return string
     */
    public function buildEmbedHtml(array $attributes = [], array $parameters = [])
    {
        // <iframe width="560" height="315" src="https://www.youtube.com/embed/5qanlirrRWs" frameborder="0" allowfullscreen></iframe>
        $attributes = array_merge($this->attributes, $attributes, [
            'src' => $this->buildEmbedUrl($parameters), // required attribute
        ]);

        $attributeStrings = [''];
        foreach ($attributes as $name => $value) {
            $attributeString = (string)$name;
            if (null !== $value) {
                $attributeString .= '="'.htmlspecialchars($value).'"';
            }
            $attributeStrings[] = $attributeString;
        }

        return '<iframe'.implode(' ', $attributeStrings).'></iframe>';
    }
}
