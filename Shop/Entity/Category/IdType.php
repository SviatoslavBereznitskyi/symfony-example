<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

/**
 * Class IdType
 */
class IdType extends GuidType
{

    public const NAME = 'shop_category_id';


    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return Id|mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new Id((string)$value) : null;
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
