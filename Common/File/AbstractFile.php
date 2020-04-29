<?php

declare(strict_types=1);

namespace App\Common\File;

/**
 * Class AbstractFile
 */
abstract class AbstractFile
{

    /**
     * @var string
     */
    protected string $value;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string
     */
    protected string $extension;

    /**
     * @var boolean
     */
    protected bool $decoded = false;


    /**
     * AbstractFile constructor.
     *
     * @param string $value
     *
     * @throws \DomainException
     */
    public function __construct(string $value)
    {
        if (preg_match('/^data:(.*)\/(\w+);base64,/', $value, $matches) === 0) {
            throw new \DomainException('Mime type not found.');
        }

        list($search, $this->type, $this->extension) = $matches;

        $this->value = str_replace($search, '', $value);

        if (false === base64_decode($this->value, true)) {
            throw new \DomainException('Base64 is not valid.');
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function decode(): string
    {
        if (true === $this->decoded) {
            return $this->value;
        }

        $this->value   = (string)base64_decode($this->value);
        $this->decoded = true;

        return $this->value;
    }

    /**
     * @return string
     */
    public function encode(): string
    {
        if (false === $this->decoded) {
            return $this->value;
        }

        $this->value   = base64_encode($this->value);
        $this->decoded = false;

        return $this->value;
    }

    /**
     * @return string
     */
    abstract public function getBaseName(): string;

    /**
     * @return string
     */
    abstract public function getDirectory(): string;

    /**
     * @return string
     */
    abstract public function getBasePath(): string;

    /**
     * @return string
     */
    abstract public function getPath(): string;

    /**
     * @return string
     */
    abstract public function getId(): string;
}
