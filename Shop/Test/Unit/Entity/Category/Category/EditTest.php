<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category;

use App\Shop\Test\Builder\Category\CategoryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers Category
 */
class EditTest extends TestCase
{
    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->build();

        $category->edit(
            $name = 'success',
            $position = 10,
            $slug = 'new',
            $description = 'success description'
        );

        self::assertEquals($name, $category->getName());
        self::assertEquals($position, $category->getPosition());
        self::assertEquals($description, $category->getDescription());
        self::assertEquals($slug, $category->getSlug());
    }

    public function testEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $category = (new CategoryBuilder())->build();

        $category->edit(
            $name = '',
            $position = 10,
            $slug = 'new',
            $description = 'success description'
        );
    }

    public function testNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $category = (new CategoryBuilder())->build();

        $category->edit(
            $name = '',
            $position = -10,
            $slug = 'new',
            $description = 'success description'
        );
    }
}
