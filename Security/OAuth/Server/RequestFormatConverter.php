<?php

declare(strict_types=1);

namespace App\Security\OAuth\Server;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestFormatConverter implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequestFormatting',
        ];
    }

    public function onRequestFormatting(KernelEvent $event): void
    {
        $route = (string)$event->getRequest()->attributes->get('_route');
        if (strpos($route, 'auth.token') === false) {
            return;
        }

        if ($event->getRequest()->request->all()) {
            return;
        }

        if (!$json = $event->getRequest()->getContent()) {
            return;
        }

        try {
            $body = (array)json_decode((string)$json, true, 2, JSON_THROW_ON_ERROR);

            $event->getRequest()->request->add($body);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException();
        }
    }
}
