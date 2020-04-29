<?php

declare(strict_types=1);

namespace App\Shop\Test\Unit\Entity\Category\Category\Attachments;


use App\Shop\Entity\Category\Attachment;
use App\Shop\Entity\Category\AttachmentType;
use App\Shop\Entity\Category\Id;
use App\Shop\Test\Builder\Category\CategoryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers Attachment
 */
class CreateTest extends TestCase
{


    public function testSuccess(): void
    {
        $attachment = new Attachment(
            $id = Id::generate(),
            $basePath = 'basePath',
            $path = 'path',
            $type = AttachmentType::preview(),
            (new CategoryBuilder())->build()
        );

        self::assertEquals($id->getValue(), $attachment->getId()->getValue());
        self::assertEquals($path, $attachment->getPath());
        self::assertEquals($type->getName(), $attachment->getType()->getName());
        self::assertEquals("$basePath/$path", $attachment->getFullPath());
        self::assertTrue($attachment->getType()->isEqualTo($type));
    }
}
