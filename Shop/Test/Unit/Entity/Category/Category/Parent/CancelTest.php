<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category\Parent;

use App\Shop\Test\Builder\Category\CategoryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers Category
 */
class CancelTest extends TestCase
{
    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->withParent()->build();

        $category->cancelSubordinate();

        self::assertNull($category->getParent());
    }

    public function testNoParent()
    {
        $category = (new CategoryBuilder())->build();
        $this->expectException(\DomainException::class);
        $category->cancelSubordinate();
    }
}
