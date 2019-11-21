<?php

namespace NotificationBundle\tests\Unit\Services;

use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Services\TelegramHookHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TelegramHookHandlerTest extends KernelTestCase
{
    public function testHandle_withEmptyData()
    {
        static::bootKernel();

        $validator = static::$kernel->getContainer()->get('validator');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $telegramHookHandler = new TelegramHookHandler($validator, $entityManager);

        $emptyData = [];

        $this->expectException(\Exception::class);

        $telegramHookHandler->handle($emptyData);
    }
}