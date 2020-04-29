<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\ChangePosition;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     schema="ChangePosityionCategory",
 *     title="Change Posityion Category",
 *     description="Params for json list",
 *     required={"position"}
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
     * @var integer
     *
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     *
     * @OA\Property(property="position", example=1234)
     */
    public int $position = -1;
}
