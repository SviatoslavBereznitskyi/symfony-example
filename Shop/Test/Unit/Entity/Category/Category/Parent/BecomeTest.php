<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category\Parent;

use App\Shop\Test\Builder\Category\CategoryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers Category
 */
class BecomeTest extends TestCase
{
    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->build();
        $parent = (new CategoryBuilder())->build();
        $category->becomeSubordinate($parent);

        self::assertEquals($parent, $category->getParent());
    }
}
