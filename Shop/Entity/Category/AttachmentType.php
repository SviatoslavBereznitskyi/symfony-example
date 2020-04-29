<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Webmozart\Assert\Assert;

/**
 * Class Preview
 */
class AttachmentType
{
    public const PREVIEW = 'preview';

    private string $name;


    /**
     * Locale constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::PREVIEW,
        ]);
        $this->name = $name;
    }

    /**
     * @return static
     */
    public static function preview(): self
    {
        return new self(self::PREVIEW);
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->name === $other->name;
    }
}
