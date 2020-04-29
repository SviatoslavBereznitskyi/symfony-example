<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Preview\Attach;

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
     * @var string
     */
    private string $basePath;

    /**
     * Handler constructor.
     *
     * @param string                      $basePath
     * @param Category\CategoryRepository $categories
     * @param Flusher                     $flusher
     */
    public function __construct(
        string $basePath,
        Category\CategoryRepository $categories,
        Flusher $flusher
    ) {
        $this->categories = $categories;
        $this->flusher    = $flusher;
        $this->basePath   = $basePath;
    }

    /**
     * @param Command $command
     *
     * @return void
     * @throws \Exception
     */
    public function handle(Command $command)
    {
        $category = $this->categories->get(new Category\Id($command->id));
        $preview  = $category->getPreview();

        if ($preview) {
            $category->removePreview();
        }

        $image = new Category\Preview($command->preview, $this->basePath, $category->getId());

        $category->attachPreview($image);

        $this->flusher->flush($category);
    }
}
