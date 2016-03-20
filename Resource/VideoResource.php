<?php

namespace LapaLabs\YoutubeHelper\Resource;

use LapaLabs\YoutubeHelper\Exception\InvalidIdException;

/**
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 */
abstract class VideoResource implements VideoResourceInterface
{
    /**
     * Valid YouTube hosts
     *
     * @var array
     */
    protected static $validHosts = [];

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
    protected $attributes = [];

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
     * @param string $host
     * @return bool
     */
    public static function isHostValid($host)
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
     * @param string $id
     *
     * @return $this
     *
     * @throws InvalidIdException
     */
    public function setId($id)
    {
        $this->id = (string)$id;

        if (!$this->isIdValid($this->id)) {
            throw new InvalidIdException($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
