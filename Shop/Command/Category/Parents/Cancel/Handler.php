<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Parents\Cancel;

use App\Flusher;
use App\Shop\Entity\Category;

/**
 * Class Handler
 */
class Handler
{

    /**
     * @var Category\CategoryRepository
     */
    private Category\CategoryRepository $categories;

    /**
     * @var Flusher
     */
    private Flusher $flusher;


    /**
     * Handler constructor.
     *
     * @param Category\CategoryRepository $categories
     * @param Flusher                     $flusher
     */
    public function __construct(Category\CategoryRepository $categories, Flusher $flusher)
    {
        $this->categories = $categories;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     *
     * @return void
     */
    public function handle(Command $command)
    {
        $category = $this->categories->get(new Category\Id($command->categoryId));

        $category->cancelSubordinate();

        $this->flusher->flush();
    }
}
