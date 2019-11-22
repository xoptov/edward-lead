<?php

namespace NotificationBundle\tests\Unit\Services;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Exceptions\NoUserWithTelegramTokenException;
use NotificationBundle\Exceptions\ValidationTelegramHookException;
use NotificationBundle\Services\TelegramHookHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validation;
use \ReflectionException;

class TelegramHookHandlerTest extends KernelTestCase
{
    public function testTelegramHookHandlerValidationError()
    {
        $this->expectException(ValidationTelegramHookException::class);

        $validator = $validator = Validation::createValidator();
        $objectManager = $this->createMock(EntityManagerInterface::class);

        $handler = new TelegramHookHandler($validator, $objectManager);

        $handler->handle([]);

    }

    /**
     * @throws NoUserWithTelegramTokenException
     * @throws ValidationTelegramHookException
     * @throws ReflectionException
     */
    public function testTelegramHookHandlerNoUserError()
    {

        $this->expectException(NoUserWithTelegramTokenException::class);

        $validator = $validator = Validation::createValidator();
        $mock = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $mock->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);

        $handler = new TelegramHookHandler($validator, $mock);

        $handler->handle([
            'message' => [
                'chat' => [
                    'id' => '13r445hg356'
                ],
                'text' => '/start ' . 'token'
            ]
        ]);

    }

    /**
     * @throws NoUserWithTelegramTokenException
     * @throws ValidationTelegramHookException
     * @throws ReflectionException
     */
    public function testTelegramHookHandler()
    {
        $validator = $validator = Validation::createValidator();

        $user = new User();
        $user->setTelegramAuthToken('token');

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($user);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);

        $handler = new TelegramHookHandler($validator, $objectManager);

        $chatId = '13r445hg356';
        $handler->handle([
            'message' => [
                'chat' => [
                    'id' => '13r445hg356'
                ],
                'text' => '/start ' . 'token'
            ]
        ]);

        $this->assertEquals($user->getTelegramChatId(), $chatId);

    }
}