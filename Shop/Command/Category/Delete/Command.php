<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Delete;

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
    public string $id;


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
