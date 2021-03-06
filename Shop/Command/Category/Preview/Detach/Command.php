<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Preview\Detach;

use Symfony\Component\Validator\Constraints as Assert;

/**
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
}
