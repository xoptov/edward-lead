<?php

namespace AppBundle\Event;

use AppBundle\Entity\Invoice;
use Symfony\Component\EventDispatcher\Event;

class InvoiceEvent extends Event
{
    const NEW_CREATED = 'invoice.new_created';
    const PROCESSED   = 'invoice.processed';

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }
}