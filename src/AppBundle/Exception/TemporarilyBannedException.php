<?php

namespace AppBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class TemporarilyBannedException extends AccountStatusException
{
}