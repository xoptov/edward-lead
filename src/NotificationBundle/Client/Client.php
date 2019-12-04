<?php

namespace NotificationBundle\Client;

use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Client
{
    /**
     * @return  Collection
     */
    abstract protected function getValidationRules(): Collection;

    /**
     * @param array $model
     *
     * @return mixed
     */
    abstract public function send(array $model);

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
    protected function validate(array $model)
    {
        $errors = $this->validator->validate($model, $this->getValidationRules());

        if (count($errors) > 0) {
            throw new ValidationNotificationClientException($errors);
        }
    }
}