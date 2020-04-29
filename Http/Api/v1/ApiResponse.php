<?php

namespace App\Http\Api\v1;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ApiResponse
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ApiResponse extends JsonResponse
{

    /**
     * ApiResponse constructor.
     *
     * @param string $message
     * @param mixed $data
     * @param array $errors
     * @param int $status
     * @param array $meta
     * @param array $headers
     * @param bool $json
     */
    public function __construct(
        string $message,
        $data = null,
        array $errors = [],
        int $status = 200,
        array $meta = [],
        array $headers = [],
        bool $json = false
    ) {
        parent::__construct($this->format($message, $data, $errors, $status, $meta), $status, $headers, $json);
    }

    /**
     * Format the API response.
     *
     * @param string $message
     * @param mixed|null $data
     * @param array $errors
     * @param int $status
     * @param array $meta
     *
     * @return array
     *
     * @psalm-suppress MissingParamType
     * @codingStandardsIgnoreStart
     */
    private function format(string $message, $data = null, array $errors = [], int $status, array $meta = []): array
    {
        // @codingStandardsIgnoreEnd
        if ($data === null) {
            $data = [];
        }

        $response = [
            'status_code' => $status,
            'message' => $message,
            'data' => $data,
            'meta' => [],
            'errors' => [],
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        if ($meta) {
            $response['meta'] = $meta;
        }

        return $response;
    }
}

    /**
     * @OA\Info(
     *     version="1.0.0",
     *     title="Symfony-skeleton API",
     *     description="HTTP JSON API",
     * ),
     *
     * /**
     * @OA\Tag(
     *   name="Auth",
     *   description="Methods for auth, signup, and password recovery for user."
     * ),
     *
     * @OA\Tag(
     *   name="Users",
     *   description="Methods for users."
     * ),
     * @OA\Tag(
     *   name="Shop",
     *   description="Methods for shop."
     * ),
     * @OA\Tag(
     *   name="Categories",
     *   description="Methods for categories."
     * ),
     * @OA\Tag(
     *   name="Manufacturer",
     *   description="Methods for manufacturer."
     * ),
     * @OA\Tag(
     *   name="Storage",
     *   description="Methods for storage."
     * ),
     * @OA\Tag(
     *   name="Options",
     *   description="Methods for options."
     * ),
     * @OA\Tag(
     *   name="Products",
     *   description="Methods for products."
     * ),
     * @OA\Tag(
     *   name="Seo",
     *   description="Methods for seo."
     * ),
     *
     * @OA\Server(
     *     url=API_V1_ENTRYPOINT,
     *     description="Api SKELETON"
     * ),
     *
     * @OA\Components(
     *     securitySchemes={
     *         @OA\SecurityScheme(
     *            name="Authorization",
     *            securityScheme="apiKey",
     *            scheme="apiKey",
     *            bearerFormat="JWT",
     *            in="header",
     *            type="apiKey",
     *            description="Bearer {access-token}",
     *            @OA\Flow(
     *                flow="password",
     *                tokenUrl=TOKEN_OAUTH2_ACTION,
     *                refreshUrl=TOKEN_REFRESH_OAUTH2_ACTION,
     *                scopes={}
     *            ),
     *         )
     *     }
     * ),
     *
     * @OA\Schema(
     *     schema="Response",
     *     type="object",
     *     @OA\Property(property="status_code", type="string"),
     *     @OA\Property(property="message", type="string"),
     *     @OA\Property(property="data", type="object", nullable=false),
     *     @OA\Property(
     *         property="meta",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Pagination")
     *     ),
     *     @OA\Property(
     *         property="errors",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ErrorModel")
     *     ),
     * ),
     *
     * @OA\Schema(
     *     schema="ErrorResponse",
     *     type="object",
     *     @OA\Property(property="status_code", type="string"),
     *     @OA\Property(property="message", type="string"),
     *     @OA\Property(property="data", type="object", nullable=false),
     *     @OA\Property(
     *         property="meta",
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(property="_pager", ref="#/components/schemas/Pagination")
     *         )
     *     ),
     *     @OA\Property(
     *         property="errors",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ErrorModel")
     *     ),
     * ),
     *
     * @OA\Schema(
     *     schema="ErrorModel",
     *     type="object",
     *     @OA\Property(property="error", type="object", nullable=true,
     *         @OA\Property(property="code", type="integer"),
     *         @OA\Property(property="message", type="string"),
     *     ),
     *     @OA\Property(property="details", type="array", nullable=true,
     *         @OA\Items(type="object", nullable=true,
     *             @OA\Property(property="propertyPath", type="string"),
     *             @OA\Property(property="title", type="string"),
     *         ),
     *     ),
     *     @OA\Property(property="info", type="string", nullable=true)
     * ),
     * @OA\Schema(
     *     schema="Pagination",
     *     type="object",
     *     nullable=true,
     *     @OA\Property(property="count", type="integer"),
     *     @OA\Property(property="total", type="integer"),
     *     @OA\Property(property="per_page", type="integer"),
     *     @OA\Property(property="page", type="integer"),
     *     @OA\Property(property="pages", type="integer"),
     * )
     */
