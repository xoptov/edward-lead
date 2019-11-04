<?php

namespace Tests\unit\AppBundle\Service;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use AppBundle\Service\ReferrerManager;

class ReferrerManagerTest extends TestCase
{
    public function testGetReferrerToken()
    {
        $rewardManager = new ReferrerManager();

        /** @var User $user */
        $user = new User();
        $user->setId(1);

        $this->assertEquals('5428ab', $rewardManager->getReferrerToken($user));
    }
}