<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * Class LocaleType
 */
class LocaleType extends StringType
{

    public const NAME = 'shop_category_locale';

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Locale ? $value->getName() : $value;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return Locale|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Locale
    {
        return !empty($value) ? new Locale((string)$value) : null;
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
