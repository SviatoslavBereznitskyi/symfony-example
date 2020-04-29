<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category\Attachments\Preview;

use App\Shop\Entity\Category\Preview;
use App\Shop\Test\Builder\Category\CategoryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers Category
 */
class DetachTest extends TestCase
{
    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->withPreview()->build();

        $category->removePreview();

        self::assertFalse($category->getPreview());
    }
}
