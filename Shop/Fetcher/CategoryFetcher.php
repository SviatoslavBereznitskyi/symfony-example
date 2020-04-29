<?php

declare(strict_types=1);

namespace App\Shop\Fetcher;

use App\Shop\Entity\Category;
use App\Shop\Fetcher\Dto\CategoryTranslation;
use App\Shop\Fetcher\Dto\TranslatedCategory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class CategoryFetcher
 */
class CategoryFetcher
{

    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var DenormalizerInterface
     */
    private DenormalizerInterface $denormalizer;

    /**
     * @var Category\CategoryRepository
     */
    private Category\CategoryRepository $categories;

    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;


    /**
     * CategoryFetcher constructor.
     *
     * @param Connection                    $connection
     * @param DenormalizerInterface         $denormalizer
     * @param Category\CategoryRepository   $categories
     * @param PaginatorInterface            $paginator
     */
    public function __construct(
        Connection $connection,
        DenormalizerInterface $denormalizer,
        Category\CategoryRepository $categories,
        PaginatorInterface $paginator
    ) {
        $this->connection   = $connection;
        $this->denormalizer = $denormalizer;
        $this->categories   = $categories;
        $this->paginator    = $paginator;
    }

    /**
     * @param Category\Id     $id
     * @param Category\Locale $locale
     *
     * @return array|object
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getTranslatedCategory(Category\Id $id, Category\Locale $locale)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'sc.id',
                'sc.slug',
                'sc.code',
                'sc.position',
                'sc.tree_root',
                'sc.parent_id',
                'sc.lft',
                'sc.lvl',
                'sc.rgt',
                'sc.name as default_name',
                'sc.description as default_description',
                'sctn.content as name',
                'sctd.content as description',
                'sctn.locale',
                'CONCAT(sca.base_path, \'/\', sca.path) as preview',
                'sctn.locale',
            )
            ->from('shop_categories', 'sc')
            ->leftJoin(
                'sc',
                'shop_categories_translations',
                'sctn',
                'sc.id = sctn.object_id AND sctn.locale = :locale AND sctn.field = :name_field'
            )
            ->leftJoin(
                'sc',
                'shop_categories_attachments',
                'sca',
                'sc.id = sca.category_id AND sca.type = :type'
            )
            ->leftJoin(
                'sc',
                'shop_categories_translations',
                'sctd',
                'sc.id = sctd.object_id AND sctd.locale = :locale AND sctd.field = :description_field'
            )
            ->andWhere('sc.id = :id')
            ->andWhere('sc.deleted_at IS NULL')
            ->setParameter(':locale', $locale->getName())
            ->setParameter(':type', Category\AttachmentType::PREVIEW)
            ->setParameter(':id', $id->getValue())
            ->setParameter(':name_field', Category\Category::NAME_FIELD)
            ->setParameter(':description_field', Category\Category::DESCRIPTION_FIELD)
            ->execute();

        $option = $qb->fetch(FetchMode::ASSOCIATIVE);

        return $this->denormalizer->denormalize(
            $option,
            TranslatedCategory::class,
            'array',
            [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
        );
    }

    /**
     * @param Category\Id $id
     *
     * @return array|object
     */
    public function getTranslationById(Category\Id $id)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'sctn.content as name',
                'sctd.content as description',
                'sctn.locale',
            )
            ->from('shop_categories_translations', 'sctn')
            ->leftJoin(
                'sctn',
                'shop_categories_translations',
                'sctd',
                'sctn.object_id = sctd.object_id AND sctn.locale = sctd.locale AND sctd.field = :description_field'
            )
            ->andWhere('sctn.object_id = :object_id')
            ->andWhere('sctn.field = :name_field')
            ->setParameter(':object_id', $id->getValue())
            ->setParameter(':name_field', Category\Category::NAME_FIELD)
            ->setParameter(':description_field', Category\Category::DESCRIPTION_FIELD)
            ->execute();

        $translations = array_map(
            fn ($translate) => $this->denormalizer->denormalize(
                $translate,
                CategoryTranslation::class,
                'array',
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            ),
            (array) $qb->fetchAll(FetchMode::ASSOCIATIVE)
        );

        return $translations;
    }


    /**
     * @param Category\Id $id
     *
     * @return Category\Category
     */
    public function get(Category\Id $id)
    {
        return $this->categories->get($id);
    }

    /**
     * @param Category\Code $code
     *
     * @return Category\Category
     */
    public function getByCode(Category\Code $code)
    {
        return $this->categories->getByCode($code);
    }

    /**
     * @return array
     */
    public function getHierarchy(): array
    {
        $hierarchy = $this->categories->getRootNodesQuery()->execute();

        return $hierarchy;
    }

    /**
     * @param int $page
     * @param int $size
     *
     * @return PaginationInterface
     */
    public function getPaginatedHierarchy(int $page, int $size): PaginationInterface
    {
        $pagination = $this->paginator->paginate(
            $this->categories->getRootNodesQuery(),
            $page,
            $size
        );

        return  $pagination;
    }

    /**
     * @param Category\Id $id
     * @param int         $page
     * @param int         $size
     *
     * @return PaginationInterface
     */
    public function getPaginatedNode(Category\Id $id, int $page, int $size): PaginationInterface
    {
        $this->categories->getNodeQuery($id);

        $pagination = $this->paginator->paginate(
            $this->categories->getNodeQuery($id),
            $page,
            $size
        );

        return  $pagination;
    }
}
