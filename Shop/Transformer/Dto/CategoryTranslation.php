<?php

declare(strict_types=1);

namespace App\Shop\Transformer\Dto;

use OpenApi\Annotations as OA;

/**
 * Class CategoryTranslation
 *
 * @OA\Schema(
 *     schema="CategoryTranslation",
 *     description="Category translation params",
 *     title="CategoryTranslation"
 * )
 */
class CategoryTranslation
{

    /**
     * @var string
     *
     * @OA\Property(property="locale")
     */
    public string $locale       = '';

    /**
     * @var string
     *
     * @OA\Property(property="name")
     */
    public string $name         = '';

    /**
     * @var string|null
     *
     * @OA\Property(property="description")
     */
    public ?string $description = '';
}

/**
 * @OA\Schema(
 *     schema="CategoryTranslationResponse",
 *     type="object",
 *     @OA\Property(property="status_code", type="string"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="data", ref="#/components/schemas/CategoryTranslation"),
 *     @OA\Property(
 *         property="meta",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Pagination")
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ErrorModel")
 *     ),
 * )
 */
