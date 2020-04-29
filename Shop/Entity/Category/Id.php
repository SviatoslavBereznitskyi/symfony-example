<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Id
 */
class Id
{

    /**
     * @var string
     */
    private string $value;


    /**
     * Id constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        Assert::uuid($value);

        $this->value = mb_strtolower($value);
    }

    /**
     * @return static
     *
     * @throws \Exception
     */
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }
}
