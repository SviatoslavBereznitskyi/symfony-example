<?php

declare(strict_types=1);

namespace App\Shop\Event;

/**
 * Class ManufacturerFileRemoved
 */
class ShopFileRemoved
{
    public string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
}
