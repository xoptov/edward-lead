<?php

namespace NotificationBundle\Model;

interface InvoiceInterface
{
    /**
     * @param int
     * 
     * @return int
     */
    public function getAmount(int $divisor = 1): int;
}