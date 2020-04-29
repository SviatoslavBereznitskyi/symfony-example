<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Parents\Become;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     schema="BecomeParentCategory",
 *     description="Params for json list",
 *     title="Become Parent Category",
 *     required={"parent_id"}
 * )
 *
 * Class Command
 */
class Command
{

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public string $categoryId = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @OA\Property(property="parent_id")
     */
    public string $parentId = '';
}
