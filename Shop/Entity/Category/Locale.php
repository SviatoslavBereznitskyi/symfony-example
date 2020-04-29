<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Webmozart\Assert\Assert;

/**
 * Class Locale
 */
class Locale
{
    public const EN_US = 'en_US';
    public const RU_RU = 'ru_RU';
    public const UK_UA = 'uk_UA';

    private string $name;


    /**
     * Locale constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf(
            $name,
            [
                self::EN_US,
                self::RU_RU,
                self::UK_UA,
            ]
        );
        $this->name = $name;
    }

    /**
     * @return static
     */
    public static function enUs(): self
    {
        return new self(self::EN_US);
    }

    /**
     * @return static
     */
    public static function ruRu(): self
    {
        return new self(self::RU_RU);
    }

    /**
     * @return static
     */
    public static function ukUA(): self
    {
        return new self(self::UK_UA);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
