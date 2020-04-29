<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class CategoryRepository
 *
 * @psalm-suppress PropertyTypeCoercion
 */
class CategoryRepository
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var \Doctrine\Persistence\ObjectRepository|NestedTreeRepository
     */
    private NestedTreeRepository $repo;

    /**
     * @var \Doctrine\DBAL\Connection|Connection
     */
    private Connection $connection;

    /**
     * CategoryRepository constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em         = $em;
        $this->repo       = $em->getRepository(Category::class);
        $this->connection = $em->getConnection();
    }

    /**
     * @param Id $id
     *
     * @return Category
     */
    public function get(Id $id): Category
    {
        /**
         * @var Category|null $category
         */
        $category = $this->repo->find($id->getValue());

        if ($category === null) {
            throw new DomainException('Category is not found.');
        }

        return $category;
    }


    /**
     * @param Code $code
     *
     * @return Category
     */
    public function getByCode(Code $code): Category
    {
        /**
         * @var Category|null $category
         */
        $category = $this->repo->findBy(['code' => $code->getValue()]);

        if ($category === null) {
            throw new DomainException('Category is not found.');
        }

        return $category;
    }

    /**
     * @param Code $code
     *
     * @return boolean
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasCode(Code $code): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.code = :code')
            ->setParameter(':code', $code->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param string $slug
     *
     * @return boolean
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasSlug(string $slug): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.slug = :slug')
            ->setParameter(':slug', $slug)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @return Code
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @psalm-suppress TooManyArguments
     */
    public function getNextCode()
    {
        return new Code((int)$this->connection->query('SELECT nextval(\'shop_category_code_seq\')')->fetchColumn());
    }

    /**
     * @return array|string
     */
    public function getHierarchy()
    {
        return $this->repo->childrenHierarchy();
    }

    /**
     * @param Id $id
     *
     * @return array|string
     */
    public function getNodeHierarchy(Id $id)
    {
        $node = $this->get($id);

        return $this->repo->childrenHierarchy($node);
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function getRootNodesQuery()
    {
        return $this->repo->getRootNodesQuery();
    }

    /**
     * @param Id $id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getNodeQuery(Id $id)
    {
        $node = $this->get($id);

        return $this->repo->getNodesHierarchyQueryBuilder($node)
            ->andWhere('node.lvl = :lvl')
            ->setParameter(':lvl', $node->getLevel() + 1)
            ->orderBy('node.position', 'ASC');
    }

    /**
     * @param Category $category
     *
     * @return void
     */
    public function add(Category $category): void
    {
        $this->em->persist($category);
    }

    /**
     * @param Category $category
     *
     * @return void
     */
    public function remove(Category $category): void
    {
        if ($category->hasChildren()) {
            throw new DomainException('Category with child node can`t be deleted.');
        }

        $this->em->remove($category);
    }

    /**
     * @param Category $category
     * @param integer $number
     *
     * @return void
     */
    public function moveUp(Category $category, int $number)
    {
        $this->repo->reorderAll();
    }
}
