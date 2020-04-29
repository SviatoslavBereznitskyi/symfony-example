<?php

namespace App\Http\Api\v1;

use DomainException;
use InvalidArgumentException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Trikoder\Bundle\OAuth2Bundle\Security\Exception\Oauth2AuthenticationFailedException;

final class ExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [['onKernelException', 10]],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {

        $throwable = $event->getThrowable();
        $request   = $event->getRequest();

        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            $response = $this->createApiResponse($throwable);
            $event->setResponse($response);
        }
    }

    /**
     * Creates the ApiResponse from any Exception
     *
     * @param \Exception $exception
     *
     * @return ApiResponse
     */
    private function createApiResponse(\Throwable $throwable)
    {
        $statusCode = $throwable instanceof HttpExceptionInterface
            ? $throwable->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        if (
            $throwable instanceof DomainException
            || $throwable instanceof AuthenticationException
            || $throwable instanceof OAuthServerException
            || $throwable instanceof InvalidArgumentException
        ) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        if ($throwable instanceof Oauth2AuthenticationFailedException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
        }

        if ($throwable instanceof AccessDeniedException) {
            $statusCode = Response::HTTP_FORBIDDEN;
        }

        $errors = [];
        if ($throwable instanceof OAuthServerException) {
            $errors[] = [
                'error' => [
                    'message' => $throwable->getErrorType(),
                    'code'    => $throwable->getCode(),
                ],
                'details' => [],
                'info' => $throwable->getMessage(),
            ];
        } elseif ($throwable instanceof UnprocessableEntityHttpException) {
            $violations = (array)json_decode((string)$throwable->getMessage(), true);
            $errors[] = [
                'error' => [
                    'message' => ResponseFactory::getMessage(ResponseFactory::ENTITY_VALIDATION_ERROR),
                    'code'    => ResponseFactory::ENTITY_VALIDATION_ERROR,
                ],
                'details' => $violations['violations'],
            ];
        } else {
            $errors[] = [
                'error' => [
                    'message' => ResponseFactory::getMessage((int)$throwable->getCode()),
                    'code'    => $throwable->getCode(),
                ],
                'details' => [],
                'info' => $throwable->getMessage(),
            ];
        }

        return new ApiResponse(ResponseFactory::FAILURE, null, $errors, $statusCode);
    }
}
