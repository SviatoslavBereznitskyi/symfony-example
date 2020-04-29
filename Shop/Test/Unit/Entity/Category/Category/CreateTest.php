<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category;

use App\Shop\Entity\Category\Category;
use App\Shop\Entity\Category\Code;
use App\Shop\Entity\Category\Id;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers Category
 */
class CreateTest  extends TestCase
{
    public function testSuccess(): void
    {
        $category = new Category(
            $id = Id::generate(),
            $code = new Code(123),
            $name = 'test',
            $position = 1,
            $description = 'test description',
            $slug = 'test'
        );

        self::assertEquals($id, $category->getId());
        self::assertEquals($code, $category->getCode());
        self::assertEquals($name, $category->getName());
        self::assertEquals($position, $category->getPosition());
        self::assertEquals($description, $category->getDescription());
        self::assertEquals($slug, $category->getSlug());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Category(
            $id = Id::generate(),
            $code = new Code(123),
            $name = '',
            $position = 1,
            $description = 'test description',
            $slug = 'test',
        );
    }
}
