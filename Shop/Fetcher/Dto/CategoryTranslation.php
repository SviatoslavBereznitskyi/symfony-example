<?php

declare(strict_types=1);

namespace App\Shop\Fetcher\Dto;

class CategoryTranslation
{
    public string $locale       = '';
    public string $name         = '';
    public ?string $description = '';
}
