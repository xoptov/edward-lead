<?php

namespace AppBundle\EventListener;

use AppBundle\Event\InvoiceEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoiceEventListener extends BaseEventListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            InvoiceEvent::PROCESSED => 'handleProcessed',
        ];
    }

    /**
     * @param InvoiceEvent $event
     *
     * @throws Exception
     */
    public function handleProcessed(InvoiceEvent $event): void
    {
        $this->emailNotificationContainer->invoiceProcessed($event->getInvoice());
    }
}