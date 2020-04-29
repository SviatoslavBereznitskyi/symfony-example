<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Webmozart\Assert\Assert;

/**
 * Class Code
 */
class Code
{

    /**
     * @var integer
     */
    private int $value;


    /**
     * Code constructor.
     *
     * @param integer $value
     */
    public function __construct(int $value)
    {
        Assert::notEmpty($value);

        $this->value = $value;
    }

    /**
     * @return integer
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
