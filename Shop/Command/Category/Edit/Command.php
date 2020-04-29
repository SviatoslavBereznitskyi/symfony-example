<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Edit;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     schema="EditCategory",
 *     description="Params for json list",
 *     title="Edit Category",
 *     required={"name", "slug"}
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
    public string $id = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @OA\Property(property="name", example="Name")
     */
    public string $name = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z0-9-]+$/",
     *     message="Is not valid slug"
     * )
     *
     * @OA\Property(property="slug", example="slug")
     */
    public string $slug = '';

    /**
     * @var string
     *
     * @OA\Property(property="description", example="Description text")
     */
    public ?string $description = null;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     *
     * @OA\Property(property="position", example="123")
     */
    public int $position = -1;


    /**
     * Command constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
