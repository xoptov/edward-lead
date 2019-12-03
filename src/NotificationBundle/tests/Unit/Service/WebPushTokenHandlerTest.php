<?php

namespace NotificationBundle\tests\Unit\Service;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Exception\NoUserWithTelegramTokenException;
use NotificationBundle\Exception\ValidationTelegramHookException;
use NotificationBundle\Exception\WebPushTokenHandlerException;
use NotificationBundle\Service\TelegramHookHandler;
use NotificationBundle\Service\WebPushTokenHandler;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validation;

class WebPushTokenHandlerTest extends KernelTestCase
{
    /**
     * @throws ReflectionException
     */
    public function testValidationError()
    {
        $this->expectException(WebPushTokenHandlerException::class);

        $validator = Validation::createValidator();
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);

        $handler = new WebPushTokenHandler($validator, $objectManager, $security);

        $handler->handle([]);
    }

    public function testTelegramHookHandler()
    {
        $user = new User();
        $validator = Validation::createValidator();
        $objectManager = $this->createMock(EntityManagerInterface::class);

        $security = $this->createMock(Security::class);
        $security
            ->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $handler = new WebPushTokenHandler($validator, $objectManager, $security);

        $token = "fk3UW95xaHY:APA91bE0m7h42yYhEwlIpOgL-8n4JzFLiDtKvHB3kdBOKkJCOXSo_-4Fy0COW1emaMFcazEfW0TuYwEozmlDFb47WDW4u7v4Hq85FZHDoIAxPUU24PCX0dE2PcXQ3Wc4Bp9AhlYgeOdf";
        $handler->handle([
            "token" => $token
        ]);

        $this->assertEquals($user->getWebPushToken(), $token);
    }
}