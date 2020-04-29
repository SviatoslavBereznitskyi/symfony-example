<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category\Translations;

use App\Shop\Entity\Category\Category;
use App\Shop\Entity\Category\CategoryTranslation;
use App\Shop\Entity\Category\Id;
use App\Shop\Entity\Category\Locale;
use App\Shop\Test\Builder\Category\CategoryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers Category
 */
class EditTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->withTranslation()->build();

        $category->editTranslation(Locale::enUs()->getName(), Category::DESCRIPTION_FIELD, 'new value');
    }
}
