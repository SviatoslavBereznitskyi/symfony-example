<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * Class FileType
 */
class AttachmentTypeType extends StringType
{

    public const NAME = 'shop_category_attachment';

    /**
     * @param AttachmentType|mixed $value
     * @param AbstractPlatform     $platform
     *
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof AttachmentType ? $value->getName() : $value;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return AttachmentType|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?AttachmentType
    {
        return !empty($value) ? new AttachmentType((string) $value) : null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return boolean
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
