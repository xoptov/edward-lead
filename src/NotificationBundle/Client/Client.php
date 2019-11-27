<?php

namespace NotificationBundle\Client;

use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Client
{
    /**
     * @return  Assert\Collection
     */
    abstract protected function getValidationRules(): Assert\Collection;

    /**
     * @param array $model
     *
     * @return mixed
     */
    abstract protected function send(array $model);

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }


    /**
     * @param array $model
     *
     * @throws ValidationNotificationClientException
     */
    public function validate(array $model)
    {
        $errors = $this->validator->validate($model, $this->getValidationRules());

        if (count($errors) > 0) {
            throw new ValidationNotificationClientException($errors);
        }
    }
}