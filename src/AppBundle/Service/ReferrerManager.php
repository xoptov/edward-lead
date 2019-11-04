<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;

class ReferrerManager
{
    /**
     * @param User $user
     *
     * @return string
     */
    public function getReferrerToken(User $user): string
    {
        $hash = sha1($user->getId(), false);

        return substr($hash, -6);
    }
}