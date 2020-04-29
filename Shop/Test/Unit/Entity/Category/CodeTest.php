<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category;

use App\Shop\Entity\Category\Code;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * @covers Code
 * Class CodeTest
 */
class CodeTest extends TestCase
{
    public function testSuccess(): void
    {
        $email = new Code($value = 123);

        self::assertEquals($value, $email->getValue());
    }
}
