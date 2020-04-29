<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category;

use App\Shop\Test\Builder\Category\CategoryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class ChangePositionTest
 */
class ChangePositionTest extends TestCase
{
    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->build();

        $category->changePosition(
            $position = 10
        );

        self::assertEquals($position, $category->getPosition());
    }

    public function testNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $category = (new CategoryBuilder())->build();

        $category->changePosition(
            $position = -10
        );
    }
}
