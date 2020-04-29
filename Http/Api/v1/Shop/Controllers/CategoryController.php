<?php

declare(strict_types=1);

namespace App\Http\Api\v1\Shop\Controllers;

use App\Http\Api\v1\PaginationSerializer;
use App\Http\Api\v1\ApiResponse;
use App\Shop\Transformer\CategoryHierarchyPaginatedTransformer;
use App\Shop\Transformer\CategoryTransformer;
use App\Shop\Command;
use App\Shop\Command\Category\Parents;
use App\Shop\Entity\Category;
use App\Shop\Fetcher\CategoryFetcher;
use App\Shop\Transformer\CategoryTranslationTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CategoryController
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class CategoryController extends AbstractController
{
    public const PER_PAGE = 20;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;


    /**
     * CategoryController constructor.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface  $validator
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator  = $validator;
    }

    /**
     * @param string          $id
     * @param Request         $request
     * @param CategoryFetcher $fetcher
     * @param Manager         $fractal
     *
     * @OA\Get(
     *     tags={"Categories"},
     *     path="/category/{id}",
     *     operationId="showCategory",
     *     description="Get category by id",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{category id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}", name="shop.category.show", methods={"GET"})
     *
     * @return Response
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show(string $id, Request $request, CategoryFetcher $fetcher, Manager $fractal): Response
    {
        $category = $fetcher->getTranslatedCategory(new Category\Id($id), new Category\Locale($request->getLocale()));

        $resource = new Item($category, CategoryTransformer::class);

        return new ApiResponse('Ok', $fractal->createData($resource)->toArray()['data']);
    }

    /**
     * @param Request $request
     * @param Command\Category\Create\Handler $handler
     * @param CategoryFetcher $fetcher ,
     * @param Manager $fractal
     *
     * @OA\Post(
     *     tags={"Categories"},
     *     path="/category",
     *     operationId="createShopCategory",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *       @OA\JsonContent(ref="#/components/schemas/CreateCategory")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category", name="shop.category.store", methods={"POST"})
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Exception
     */
    public function store(
        Request $request,
        Command\Category\Create\Handler $handler,
        CategoryFetcher $fetcher,
        Manager $fractal
    ): Response {
        /**
         * @var Command\Category\Create\Command $command
         */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command\Category\Create\Command::class,
            'json',
            [
                'object_to_populate' => new Command\Category\Create\Command($id = Category\Id::generate()->getValue()),
                'ignored_attributes' => ['id'],
            ]
        );

        $violation = $this->validator->validate($command);
        if (0 !== $violation->count()) {
            $json = $this->serializer->serialize($violation, 'json');

            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        $category = $fetcher->getTranslatedCategory(
            new Category\Id($id),
            new Category\Locale($request->getLocale())
        );

        $resource = new Item($category, CategoryTransformer::class);

        return new ApiResponse(
            'Category created.',
            $fractal->createData($resource)->toArray()['data'],
            [],
            Response::HTTP_CREATED
        );
    }

    /**
     * @param string                        $id
     * @param Request                       $request
     * @param Command\Category\Edit\Handler $handler
     *
     * @OA\Put(
     *     tags={"Categories"},
     *     path="/category/{id}",
     *     operationId="editCategory",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{category id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *       @OA\JsonContent(ref="#/components/schemas/EditCategory")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}", name="shop.category.edit", methods={"PUT"})
     *
     * @return Response
     */
    public function edit(string $id, Request $request, Command\Category\Edit\Handler $handler): Response
    {
        /**
         * @var Command\Category\Edit\Command $command
         */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command\Category\Edit\Command::class,
            'json',
            [
                'object_to_populate' => new Command\Category\Edit\Command($id),
                'ignored_attributes' => ['id'],
            ]
        );

        $violation = $this->validator->validate($command);
        if (0 !== count($violation)) {
            $json = $this->serializer->serialize($violation, 'json');

            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return new ApiResponse('Category edited.');
    }

    /**
     * @param string                          $id
     * @param Command\Category\Delete\Handler $handler
     *
     * @OA\Delete(
     *     path="/category/{id}",
     *     tags={"Categories"},
     *     operationId="deleteCategory",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{category id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}", name="shop.category.delete", methods={"DELETE"})
     *
     * @return Response
     */
    public function delete(string $id, Command\Category\Delete\Handler $handler): Response
    {
        $command = new Command\Category\Delete\Command($id);

        $violation = $this->validator->validate($command);
        if (0 !== count($violation)) {
            $json = $this->serializer->serialize($violation, 'json');

            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return new ApiResponse('Category deleted.', [], [], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string          $id
     * @param CategoryFetcher $fetcher
     * @param Manager         $fractal
     *
     * @OA\Get(
     *     path="/category/{id}/translation",
     *     tags={"Categories"},
     *     operationId="getAllCategoryTranslation",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{category id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryTranslationResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/translation", name="shop.category.translation.all", methods={"GET"})
     *
     * @return Response
     */
    public function allTranslation(string $id, CategoryFetcher $fetcher, Manager $fractal): Response
    {
        $categoryTranslation = $fetcher->getTranslationById(new Category\Id($id));

        $resource = new Collection($categoryTranslation, CategoryTranslationTransformer::class);

        return new ApiResponse('Ok', $fractal->createData($resource)->toArray()['data']);
    }

    /**
     * @param string                                    $id
     * @param Request                                   $request
     * @param Command\Category\Translations\Add\Handler $handler
     *
     * @OA\Post(
     *     tags={"Categories"},
     *     path="/category/{id}/translation",
     *     operationId="addTranslationCategory",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{category id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *       @OA\JsonContent(ref="#/components/schemas/AddCategoryTranslation")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/translation", name="shop.category.add-translation", methods={"POST"})
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function addTranslation(
        string $id,
        Request $request,
        Command\Category\Translations\Add\Handler $handler
    ): Response {
        /**
         * @var Command\Category\Translations\Add\Command $command
         */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command\Category\Translations\Add\Command::class,
            'json',
            [
                'object_to_populate' => new Command\Category\Translations\Add\Command($id),
                'ignored_attributes' => ['id'],
            ]
        );

        $violation = $this->validator->validate($command);
        if (0 !== $violation->count()) {
            $json = $this->serializer->serialize($violation, 'json');

            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return new ApiResponse('Category translation added.', [], [], Response::HTTP_CREATED);
    }

    /**
     * @param string                                     $id
     * @param Request                                    $request
     * @param Command\Category\Translations\Edit\Handler $handler
     *
     * @OA\Put(
     *     tags={"Categories"},
     *     path="/category/{id}/translation",
     *     operationId="editTranslationCategory",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{category id}",
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\RequestBody(
     *       @OA\JsonContent(ref="#/components/schemas/EditCategoryTranslation")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/translation", name="shop.category.edit-translation", methods={"PUT"})
     *
     * @return Response
     */
    public function editTranslation(
        string $id,
        Request $request,
        Command\Category\Translations\Edit\Handler $handler
    ): Response {
        /**
         * @var Command\Category\Translations\Edit\Command $command
         */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command\Category\Translations\Edit\Command::class,
            'json',
            [
                'object_to_populate' => new Command\Category\Translations\Edit\Command($id),
                'ignored_attributes' => ['id'],
            ]
        );

        $violation = $this->validator->validate($command);
        if (0 !== $violation->count()) {
            $json = $this->serializer->serialize($violation, 'json');

            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return new ApiResponse('Category translation edited.');
    }

    /**
     * @param string                 $id
     * @param Request                $request
     * @param Parents\Become\Handler $handler
     *
     * @OA\Post(
     *     path="/category/{id}/subordination",
     *     tags={"Categories"},
     *     operationId="BecomeSubordinationCategory",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *       @OA\JsonContent(ref="#/components/schemas/BecomeParentCategory")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/subordination", name="shop.category.subordination.become", methods={"POST"})
     *
     * @return Response
     */
    public function becomeSubordinate(string $id, Request $request, Parents\Become\Handler $handler): Response
    {
        /** @var Parents\Become\Command $command */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Parents\Become\Command::class,
            'json'
        );

        $command->categoryId = $id;

        $violation = $this->validator->validate($command);
        if (0 !== count($violation)) {
            $json = $this->serializer->serialize($violation, 'json');

            throw new UnprocessableEntityHttpException($json);
        }

        $handler->handle($command);

        return new ApiResponse('Category become subordinate.');
    }

    /**
     * @param string                 $id
     * @param Parents\Cancel\Handler $handler
     *
     * @OA\Delete(
     *     path="/category/{id}/subordination",
     *     tags={"Categories"},
     *     operationId="CancelSubordinationCategory",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/subordination", name="shop.category.subordination.cancel", methods={"DELETE"})
     *
     * @return Response
     */
    public function cancelSubordination(string $id, Parents\Cancel\Handler $handler): Response
    {
        $command = new Parents\Cancel\Command();

        $command->categoryId = $id;

        $violation = $this->validator->validate($command);
        if (0 !== count($violation)) {
            $json = $this->serializer->serialize($violation, 'json');

            throw new UnprocessableEntityHttpException($json);
        }

        $handler->handle($command);

        return new ApiResponse('Category cancel subordinate.', [], [], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param CategoryFetcher $fetcher
     * @param Manager $fractal
     *

     * @OA\Get(
     *     tags={"Categories"},
     *     path="/category/hierarchy/show-all",
     *     operationId="showAllCategoryHierarchy",
     *     description="Get category hierarchy",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryHierarchyResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/hierarchy/show-all", name="shop.category.hierarchy.show", methods={"GET"})
     *
     * @return Response
     */
    public function showHierarchy(CategoryFetcher $fetcher, Manager $fractal): Response
    {
        $hierarchy = $fetcher->getHierarchy();

        $resource = new Collection($hierarchy, CategoryHierarchyPaginatedTransformer::class);

        return new ApiResponse('Ok', $fractal->createData($resource)->toArray()['data']);
    }

    /**
     * @param Request         $request
     * @param CategoryFetcher $fetcher
     * @param Manager         $fractal
     *

     * @OA\Get(
     *     tags={"Categories"},
     *     path="/category/hierarchy/show",
     *     operationId="showCategoryHierarchy",
     *     description="Get paginated category hierarchy",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryHierarchyResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/hierarchy/show", name="shop.category.hierarchy.paginate", methods={"GET"})
     *
     * @return Response
     */
    public function showPaginateHierarchy(Request $request, CategoryFetcher $fetcher, Manager $fractal): Response
    {
        $pagination = $fetcher->getPaginatedHierarchy(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
        );

        $resource = new Collection($pagination->getItems(), CategoryHierarchyPaginatedTransformer::class);

        return new ApiResponse(
            'Ok',
            $fractal->createData($resource)->toArray()['data'],
            [],
            Response::HTTP_OK,
            ['_pager' => PaginationSerializer::toArray($pagination)]
        );
    }

    /**
     * @param string $id
     * @param Request $request
     * @param CategoryFetcher $fetcher
     * @param Manager $fractal
     *

     * @OA\Get(
     *     tags={"Categories"},
     *     path="/category/hierarchy/{id}",
     *     operationId="showNodeCategoryHierarchy",
     *     description="Get paginated category node hierarchy",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryHierarchyResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/hierarchy/{id}", name="shop.category.hierarchy.showNode", methods={"GET"})
     *
     * @return Response
     */
    public function showNodeHierarchy(
        string $id,
        Request $request,
        CategoryFetcher $fetcher,
        Manager $fractal
    ): Response {
        $pagination = $fetcher->getPaginatedNode(
            new Category\Id($id),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
        );

        $resource = new Collection($pagination->getItems(), CategoryHierarchyPaginatedTransformer::class);

        return new ApiResponse(
            'Ok',
            $fractal->createData($resource)->toArray()['data'],
            [],
            Response::HTTP_OK,
            ['_pager' => PaginationSerializer::toArray($pagination)]
        );
    }

    /**
     * @param string                                  $id
     * @param Request                                 $request
     * @param Command\Category\ChangePosition\Handler $handler
     *

     * @OA\Patch(
     *     tags={"Categories"},
     *     path="/category/{id}/position",
     *     operationId="changeCategoryPosition",
     *     description="change Category Position",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *       @OA\JsonContent(ref="#/components/schemas/ChangePosityionCategory")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/position", name="shop.category.position.change", methods={"PATCH"})
     *
     * @return Response
     */
    public function changePosition(
        string $id,
        Request $request,
        Command\Category\ChangePosition\Handler $handler
    ): Response {
        /**
         * @var Command\Category\ChangePosition\Command $command
         */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command\Category\ChangePosition\Command::class,
            'json'
        );

        $command->id = $id;

        $violation = $this->validator->validate($command);
        if (0 !== count($violation)) {
            $json = $this->serializer->serialize($violation, 'json');

            throw new UnprocessableEntityHttpException($json);
        }

        $handler->handle($command);

        return new ApiResponse('Category position was changed.');
    }


    /**
     * @param string                                  $id
     * @param Request                                 $request
     * @param Command\Category\Preview\Attach\Handler $handler
     *
     * @OA\Post(
     *     path="/category/{id}/preview",
     *     tags={"Categories"},
     *     operationId="AttachCategoryPreview",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *       @OA\JsonContent(ref="#/components/schemas/AttachCategoryPreview")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/preview", name="shop.category.preview.attach", methods={"POST"})
     *
     * @return Response
     * @throws \Exception
     */
    public function attachPreview(string $id, Request $request, Command\Category\Preview\Attach\Handler $handler)
    {
        /**
         * @var Command\Category\Preview\Attach\Command $command
         */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command\Category\Preview\Attach\Command::class,
            'json'
        );

        $command->id = $id;

        $violation = $this->validator->validate($command);
        if (0 !== count($violation)) {
            $json = $this->serializer->serialize($violation, 'json');

            throw new UnprocessableEntityHttpException($json);
        }

        $handler->handle($command);

        return new ApiResponse('Preview was attached.');
    }

    /**
     * @param string $id
     * @param Command\Category\Preview\Detach\Handler $handler
     *
     * @OA\DELETE(
     *     path="/category/{id}/preview",
     *     tags={"Categories"},
     *     operationId="DetachCategoryPreview",
     *     method="application/json",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=false,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="{lang}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="{id}",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     *
     * @Route("/category/{id}/preview", name="shop.category.preview.detach", methods={"DELETE"})
     *
     * @return Response
     * @throws \Exception
     */
    public function detachPreview(string $id, Command\Category\Preview\Detach\Handler $handler)
    {
        $command = new Command\Category\Preview\Detach\Command();

        $command->id = $id;

        $violation = $this->validator->validate($command);
        if (0 !== count($violation)) {
            $json = $this->serializer->serialize($violation, 'json');

            throw new UnprocessableEntityHttpException($json);
        }

        $handler->handle($command);

        return new ApiResponse('Preview was detached.');
    }
}
