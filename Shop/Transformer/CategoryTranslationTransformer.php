<?php

declare(strict_types=1);

namespace App\Shop\Transformer;

use App\Shop\Fetcher\Dto\CategoryTranslation;
use App\Shop\Transformer\Dto;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CategoryTranslationTransformer
 */
class CategoryTranslationTransformer extends TransformerAbstract
{

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;


    /**
     * CategoryTransformer constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param CategoryTranslation $categoryTranslation
     *
     * @return mixed
     */
    public function transform(CategoryTranslation $categoryTranslation): array
    {
        $dto = new Dto\CategoryTranslation();

        $dto->locale      = $categoryTranslation->locale;
        $dto->name        = $categoryTranslation->name;
        $dto->description = $categoryTranslation->description;

        return $this->serializer->normalize($dto);
    }
}
