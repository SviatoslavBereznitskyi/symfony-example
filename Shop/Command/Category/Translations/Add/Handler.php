<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Translations\Add;

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
    public function __construct(
        Category\CategoryRepository $categories,
        Flusher $flusher
    ) {
        $this->categories = $categories;
        $this->flusher    = $flusher;
    }

    /**
     * @param Command $command
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $category = $this->categories->get(new Category\Id($command->id));

        $category->addTranslation(
            new Category\CategoryTranslation(
                Category\Id::generate(),
                new Category\Locale($command->locale),
                Category\Category::NAME_FIELD,
                $command->name
            )
        );

        $category->addTranslation(
            new Category\CategoryTranslation(
                Category\Id::generate(),
                new Category\Locale($command->locale),
                Category\Category::DESCRIPTION_FIELD,
                (string) $command->description
            )
        );

        $this->flusher->flush();
    }
}
