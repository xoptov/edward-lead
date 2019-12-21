<?php

namespace AppBundle\Entity\User\Personal;

class Passport
{
    /**
     * @var string|null
     */
    private $serialNumber;

    /**
     * @var string|null
     */
    private $issuer;

    /**
     * @var \DateTime|null
     */
    private $issueDate;

    /**
     * @var string|null
     */
    private $permanentAddress;
}