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
class AddTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $category = (new CategoryBuilder())->build();

        $category->addTranslation(
            $translation = new CategoryTranslation(
                Id::generate(),
                Locale::enUs(),
                Category::DESCRIPTION_FIELD,
                'test'
            )
        );
    }

    public function testExist(): void
    {
        $option = (new CategoryBuilder())->withTranslation()->build();

        $this->expectExceptionMessage('Translation for "' . Category::DESCRIPTION_FIELD . '" already exists');
        $option->addTranslation(new CategoryTranslation(
            Id::generate(),
            Locale::enUs(),
            Category::DESCRIPTION_FIELD,
            'value'
        ));
    }
}
