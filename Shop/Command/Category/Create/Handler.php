<?php

declare(strict_types=1);

namespace App\Shop\Command\Category\Create;

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
        $this->flusher    = $flusher;
    }

    /**
     * @param Command $command
     *
     * @return void
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function handle(Command $command)
    {
        if (null !== $command->slug && true === $this->categories->hasSlug($command->slug)) {
            throw new \DomainException('Slug is already exist!');
        }

        $category = new Category\Category(
            new Category\Id($command->id),
            $this->categories->getNextCode(),
            $command->name,
            $command->position,
            $command->description,
            $command->slug,
        );

        $this->categories->add($category);

        $this->flusher->flush();
    }
}
