<?php

declare(strict_types=1);

namespace App\Event\Listener\Shop\Customer;

use App\Auth\Event\User\PhoneChanged;
use App\Flusher;
use App\Shop\Entity\Customer\CustomerRepository;
use App\Shop\Entity\Customer\Id;
use App\Shop\Entity\Customer\Name;
use App\Shop\Entity\Customer\Phone;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PhoneChangedListener
 */
class PhoneChangedListener implements EventSubscriberInterface
{

    /**
     * @var Flusher
     */
    private Flusher $flusher;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customers;


    /**
     * PhoneChangedListener constructor.
     *
     * @param Flusher $flusher
     * @param CustomerRepository $customers
     */
    public function __construct(Flusher $flusher, CustomerRepository $customers)
    {
        $this->flusher   = $flusher;
        $this->customers = $customers;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PhoneChanged::class => 'onUserNameChanged',
        ];
    }

    /**
     * @param PhoneChanged $event
     */
    public function onUserNameChanged(PhoneChanged $event)
    {
        $customer = $this->customers->get(new Id($event->id));

        $customer->changePhone(new Phone($event->phone));

        $this->flusher->flush();
    }
}
