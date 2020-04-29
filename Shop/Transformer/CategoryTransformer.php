<?php

declare(strict_types=1);

namespace App\Shop\Transformer;

use App\Shop\Transformer\Dto\Category;
use App\Shop\Fetcher\Dto\TranslatedCategory;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class CategoryTransformer
 */
class CategoryTransformer extends TransformerAbstract
{

    /**
     * @var SerializerInterface|NormalizerInterface
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
     * @param TranslatedCategory $translatedCategory
     *
     * @return mixed
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function transform(TranslatedCategory $translatedCategory): array
    {
        $dto = new Category();

        $dto->id          = $translatedCategory->id;
        $dto->code        = $translatedCategory->code;
        $dto->slug        = $translatedCategory->slug;
        $dto->preview     = $translatedCategory->preview;
        $dto->position    = $translatedCategory->position;

        $dto->name = $translatedCategory->defaultName;
        if ($translatedCategory->name !== null) {
            $dto->name = $translatedCategory->name;
        }

        $dto->description = $translatedCategory->defaultName;
        if ($translatedCategory->description !== null) {
            $dto->description = $translatedCategory->description;
        }

        return $this->serializer->normalize($dto);
    }
}
