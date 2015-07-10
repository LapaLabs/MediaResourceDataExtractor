<?php

namespace LapaLabs\YoutubeHelper\Resource;

/**
 * Class YoutubeResource
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License
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
     * @param string $resourceId
     */
    public function __construct($resourceId)
    {
        $this->setId($resourceId);
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
     */
    public static function create($resourceId)
    {
        return new static($resourceId);
    }

    /**
     * @param $resourceUrl
     * @return static
     */
    public static function createFromUrl($resourceUrl)
    {
        if (false === filter_var($resourceUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('The given argument is invalid URL.');
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
                        throw new \InvalidArgumentException('The given YouTube resource URL do not contain a valid ID');
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
                        throw new \InvalidArgumentException('The given YouTube resource URL do not contain a valid ID');
                    }

                    break;

                default:
                    throw new \InvalidArgumentException('The given argument is invalid YouTube resource URL.');
            }
        } else {
            throw new \InvalidArgumentException('The given argument is invalid URL.');
        }

        return new static($resourceId);
    }

    /**
     * @param string $host
     * @return string
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
                throw new \InvalidArgumentException('The given argument is invalid YouTube host.');
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
        return $this->buildUrlForHost(static::HOST_DEFAULT);
    }

    /**
     * @return string
     */
    public function buildAliasUrl()
    {
        return $this->buildUrlForHost(static::HOST_ALIAS);
    }

    /**
     * @return string
     */
    public function buildMobileUrl()
    {
        return $this->buildUrlForHost(static::HOST_MOBILE);
    }

    /**
     * @return string
     */
    public function buildShortUrl()
    {
        return $this->buildUrlForHost(static::HOST_SHORT);
    }

    /**
     * @return string
     */
    public function buildEmbedUrl()
    {
        // https://www.youtube.com/embed/5qanlirrRWs
        return 'https://' . static::HOST_DEFAULT . '/embed/' . $this->id;
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
            $attributeString = trim($name);
            if (null !== $value) {
                $attributeString .= '="' . htmlspecialchars(trim($value)) . '"';
            }
            $attributeStrings[] = $attributeString;
        }

        return '<iframe' . implode(' ', $attributeStrings) . '></iframe>';
    }

    /**
     * @param string $resourceId
     * @return bool
     */
    public static function isValidId($resourceId)
    {
        return 11 === strlen($resourceId);
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
     * @param string $resourceId
     * @return $this
     */
    protected function setId($resourceId)
    {
        if (!$this->isValidId($resourceId)) {
            throw new \InvalidArgumentException('The given argument is invalid YouTube ID.');
        }

        $this->id = $resourceId;

        return $this;
    }

    /**
     * @param array $attributes
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
