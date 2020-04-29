<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BigIntType;

/**
 * Class CodeType
 */
class CodeType extends BigIntType
{
    public const NAME = 'shop_category_code';


    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return int|mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Code ? $value->getValue() : $value;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return Code|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Code
    {
        return !empty($value) ? new Code((int)$value) : null;
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
