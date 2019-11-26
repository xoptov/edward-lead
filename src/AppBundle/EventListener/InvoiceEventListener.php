<?php

namespace AppBundle\EventListener;

use AppBundle\Event\InvoiceEvent;
use AppBundle\Notifications\InvoiceProcessedNotification;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoiceEventListener implements EventSubscriberInterface
{
    /**
     * @var InvoiceProcessedNotification
     */
    private $invoiceProcessedNotification;

    /**
     * InvoiceEventListener constructor.
     *
     * @param InvoiceProcessedNotification $invoiceProcessedNotification
     */
    public function __construct(InvoiceProcessedNotification $invoiceProcessedNotification)
    {
        $this->invoiceProcessedNotification = $invoiceProcessedNotification;
    }

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
        $this->invoiceProcessedNotification->send($event->getInvoice());
    }
}