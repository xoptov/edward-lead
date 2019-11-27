<?php

namespace NotificationBundle\Client;

use Brownie\ESputnik\ESputnik;
use Brownie\ESputnik\HTTPClient\HTTPClient;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class EsputnikClient extends Client
{
    /**
     * @var HTTPClient
     */
    public $httpClient;

    /**
     * EsputnikClient constructor.
     *
     * @param HTTPClient         $httpClient
     * @param ValidatorInterface $validator
     */
    public function __construct(HTTPClient $httpClient, ValidatorInterface $validator)
    {
        $this->httpClient = $httpClient;

        parent::__construct($validator);
    }
}