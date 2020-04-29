<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use App\Shop\Entity\Category\AttachmentType;

/**
 * @covers AttachmentType
 */
class AttachmentTypeTest extends TestCase
{


    public function testSuccess(): void
    {
        $attachmentType = new AttachmentType($value = 'preview');

        self::assertEquals($value, $attachmentType->getName());
    }

    public function testIsEqual(): void
    {
        $attachmentType = new AttachmentType($value = 'preview');

        self::assertTrue($attachmentType->isEqualTo(new AttachmentType($value)));
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AttachmentType('image');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AttachmentType('');
    }
}
