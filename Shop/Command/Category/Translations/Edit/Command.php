<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Translations\Edit;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     schema="EditCategoryTranslation",
 *     description="Params for json list",
 *     title="Edit Category Translation",
 *     required={
 *          "locale",
 *          "name"
 *     }
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
     * @OA\Property(property="locale", example="en_US")
     */
    public string $locale = '';

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
     * @OA\Property(property="description", example="description text")
     */
    public ?string $description = null;


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
