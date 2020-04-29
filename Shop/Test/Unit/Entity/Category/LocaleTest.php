<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category;

use App\Shop\Entity\Category\Locale;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locale
 */
class LocaleTest extends TestCase
{
    public function testSuccess(): void
    {
        $locale = new Locale($name = Locale::EN_US);

        self::assertEquals($name, $locale->getName());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Locale('none');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Locale('');
    }

    public function testCompanyFactory(): void
    {
        $locale = Locale::enUs();

        self::assertEquals(Locale::EN_US, $locale->getName());
    }
}
