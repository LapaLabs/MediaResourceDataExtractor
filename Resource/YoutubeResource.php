<?php

namespace LapaLabs\YoutubeHelper\Resource;

use LapaLabs\YoutubeHelper\Exception\InvalidHostException;
use LapaLabs\YoutubeHelper\Exception\InvalidIdException;
use LapaLabs\YoutubeHelper\Exception\InvalidUrlException;

/**
 * Class YoutubeResource
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 */
class YoutubeResource
{
    const YOUTU_BE          = 'youtu.be';
    const YOUTUBE_COM       = 'youtube.com';
    const M_YOUTUBE_COM     = 'm.youtube.com';
    const WWW_YOUTUBE_COM   = 'www.youtube.com';
    const HOST_DEFAULT      = self::WWW_YOUTUBE_COM;
    const HOST_ALIAS        = self::YOUTUBE_COM;
    const HOST_MOBILE       = self::M_YOUTUBE_COM;
    const HOST_SHORT        = self::YOUTU_BE;

    /**
     * Valid YouTube hosts
     *
     * @var array
     */
    protected static $validHosts = [
        self::YOUTU_BE,
        self::YOUTUBE_COM,
        self::M_YOUTUBE_COM,
        self::WWW_YOUTUBE_COM,
    ];

    /**
     * @var string
     */
    protected $id;

    /**
     * URL query parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * IFrame HTML tag attributes
     *
     * @var array
     */
    protected $attributes = [
        'width'           => 560,
        'height'          => 315,
        'src'             => '', // hold position for specific order
        'frameborder'     => 0,
        'allowfullscreen' => null,
    ];

    /**
     * @param string $id
     * @throws InvalidIdException
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }

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
                        && static::isValidId($output['v'])
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
     * @param string $host
     * @return string
     * @throws InvalidHostException
     */
    protected function buildUrlForHost($host)
    {
        switch ($host) {
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

        return 'https://' . $host . $path . $this->id;
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

        // https://www.youtube.com/embed/5qanlirrRWs?controls=0&amp;autoplay=1
        $url = 'https://'.static::HOST_DEFAULT.'/embed/'.$this->id;
        if (count($parameters)) {
            $parameterStrings = [];
            foreach ($parameters as $name => $value) {
                $parameterString = (string)$name;
                if (null !== $value) {
                    $parameterString .= '=' . htmlspecialchars($value);
                }
                $parameterStrings[] = $parameterString;
            }
            $url .= '?'.implode('&amp;', $parameterStrings);
        }

        return $url;
    }

    /**
     * The valid HTML code to embed this resource
     *
     * @param array $attributes
     * @return string
     */
    public function buildEmbedCode(array $attributes = [])
    {
        // <iframe width="560" height="315" src="https://www.youtube.com/embed/5qanlirrRWs" frameborder="0" allowfullscreen></iframe>
        $attributes = array_merge($this->attributes, $attributes, [
            'src' => $this->buildEmbedUrl(), // required attribute
        ]);

        $attributeStrings = [''];
        foreach ($attributes as $name => $value) {
            $attributeString = (string)$name;
            if (null !== $value) {
                $attributeString .= '="' . htmlspecialchars($value) . '"';
            }
            $attributeStrings[] = $attributeString;
        }

        return '<iframe' . implode(' ', $attributeStrings) . '></iframe>';
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public static function isValidId($id)
    {
        return 11 === strlen($id);
    }

    /**
     * @param string $host
     * @return bool
     */
    public static function isValidHost($host)
    {
        return in_array(strtolower($host), static::$validHosts);
    }

    /**
     * @return array
     */
    public static function getValidHosts()
    {
        return static::$validHosts;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     *
     * @throws InvalidIdException
     */
    protected function setId($id)
    {
        $this->id = $id;

        if (!static::isValidId($this->id)) {
            throw new InvalidIdException($this);
        }

        return $this;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
