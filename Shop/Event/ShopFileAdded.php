<?php

declare(strict_types=1);

namespace App\Shop\Event;

use  App\Common\File\AbstractFile;

/**
 * Class ShopFileAdded
 */
class ShopFileAdded
{
    public AbstractFile $file;

    public function __construct(AbstractFile $file)
    {
        $this->file = $file;
    }
}
