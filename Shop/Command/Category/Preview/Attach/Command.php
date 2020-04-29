<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Preview\Attach;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     schema="AttachCategoryPreview",
 *     description="Params for json list",
 *     title="Attach Category Preview",
 *     required={"preview"}
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
     * @OA\Property(property="preview", example="base64:string")
     */
    public string $preview = '';
}
