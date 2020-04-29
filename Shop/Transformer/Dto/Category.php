<?php

declare(strict_types=1);

namespace App\Shop\Transformer\Dto;

use OpenApi\Annotations as OA;

/**
 * Class FullCategory
 *
 * @OA\Schema(
 *     schema="Category",
 *     description="Category params",
 *     title="Category"
 * )
 */
class Category
{

    /**
     * @var string
     *
     * @OA\Property(property="id")
     */
    public string $id = '';

    /**
     * @var integer
     *
     * @OA\Property(property="code")
     */
    public int $code = 0;

    /**
     * @var string
     *
     * @OA\Property(property="slug")
     */
    public string $slug = '';

    /**
     * @var string
     *
     * @OA\Property(property="name")
     */
    public string $name = '';

    /**
     * @var string
     *
     * @OA\Property(property="description")
     */
    public string $description = '';

    /**
     * @var integer
     *
     * @OA\Property(property="position")
     */
    public int $position = 0;

    /**
     * @var string
     *
     * @OA\Property(property="preview")
     */
    public string $preview = '';
}

/**
 * @OA\Schema(
 *     schema="CategoryResponse",
 *     type="object",
 *     @OA\Property(property="status_code", type="string"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="data", ref="#/components/schemas/Category"),
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
