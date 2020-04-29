<?php

declare(strict_types=1);

namespace App\Http\Api\v1;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LocaleListener
 */
class LocaleListener implements EventSubscriberInterface
{

    public const RU_RU = 'ru_RU';
    public const EN_US = 'en_US';
    public const UK_UA = 'uk_UA';

    private array $locales = [
        self::RU_RU,
        self::EN_US,
        self::UK_UA,
    ];


    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param RequestEvent $event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $locale = $request->getPreferredLanguage();

        if (empty($locale)) {
            $request->setLocale($this->getDefaultLocale());

            return;
        }

        if (false === in_array($locale, $this->locales, true)) {
            $locale = $this->getDefaultLocale();
        }

        $request->setLocale($locale);
    }

    /**
     * @return string
     */
    private function getDefaultLocale(): string
    {
        return self::EN_US;
    }
}
