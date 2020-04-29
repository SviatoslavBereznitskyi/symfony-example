<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category\Attachments\Preview;

use App\Shop\Entity\Category\Preview;
use App\Shop\Test\Builder\Category\CategoryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers Category
 */
class AttachTest extends TestCase
{
    private const BASE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAA3NCSVQICAjb4U/gAAAAEHRFWHRTb2Z0d2FyZQBTaHV0dGVyY4LQCQAAABZJREFUCNdj/P//PwMDAxMDAwMDAwMAJAYDAb0e47oAAAAASUVORK5CYII=';

    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->build();

        $category->attachPreview(
            $preview = new Preview(self::BASE, 'localhost', $category->getId())
        );

        self::assertEquals($category->getPreview()->getPath(), $preview->getPath());
    }
}
