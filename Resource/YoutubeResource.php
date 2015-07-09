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
     * @param string $resourceId
     */
    public function __construct($resourceId)
    {
        $this->id = $this->setId($resourceId);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }

    public static function create($resourceId)
    {
        return new static($resourceId);
    }

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
                        and true === isset($parsedUrl['path'])
                        and true === isset($parsedUrl['query'])
                        and 0 === strcmp('/watch', $parsedUrl['path'])
                        and null == parse_str($parsedUrl['query'], $output)
                        and isset($output['v'])
                        and static::isValidId($output['v'])
                    ) {
                        $resourceId = $output['v'];
                    } elseif (true
                        and true === isset($parsedUrl['path'])
                        and 1 === preg_match('@^/embed/(?<v>[\w-]{11})($|/|#|\?)@', $parsedUrl['path'], $matches)
                    ) {
                        $resourceId = $matches['v'];
                    } else {
                        throw new \InvalidArgumentException('The given YouTube resource URL do not contain a valid ID');
                    }

                    break;

                // https://youtu.be/5qanlirrRWs
                case static::YOUTU_BE:
                    if (true
                        and true === isset($parsedUrl['path'])
                        and 1 === preg_match('@^/(?<v>[\w-]{11})($|/|#|\?)@', $parsedUrl['path'], $matches)
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


    public function buildUrl()
    {
        return $this->buildDefaultUrl();
    }

    public function buildDefaultUrl()
    {
        return $this->buildUrlForHost(static::HOST_DEFAULT);
    }

    public function buildAliasUrl()
    {
        return $this->buildUrlForHost(static::HOST_ALIAS);
    }

    public function buildMobileUrl()
    {
        return $this->buildUrlForHost(static::HOST_MOBILE);
    }

    public function buildShortUrl()
    {
        return $this->buildUrlForHost(static::HOST_SHORT);
    }

    public function buildEmbedUrl()
    {
        // https://www.youtube.com/embed/5qanlirrRWs
        return 'https://' . static::HOST_DEFAULT . '/embed/' . $this->id;
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
     */
    protected function setId($resourceId)
    {
        if (!$this->isValidId($resourceId)) {
            throw new \InvalidArgumentException('The given argument is invalid YouTube ID.');
        }

        $this->id = $resourceId;
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
}
