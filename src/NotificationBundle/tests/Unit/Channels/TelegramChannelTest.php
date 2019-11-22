<?php

namespace Tests\Unit\Channels;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use NotificationBundle\ChannelModels\Telegram;
use NotificationBundle\Channels\TelegramChannel;
use NotificationBundle\Clients\TelegramClient;
use NotificationBundle\Event\ConfigureTelegramEvent;
use NotificationBundle\EventSubscriber\ConfigureTelegramEventSubscriber;
use NotificationBundle\Exceptions\NoUserWithTelegramTokenException;
use NotificationBundle\Exceptions\ValidationTelegramHookException;
use NotificationBundle\Services\TelegramHookHandler;
use NotificationBundle\tests\Functional\BaseFunctional;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validation;
use Doctrine\ORM\EntityManagerInterface;

class TelegramChannelTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testSubscriber()
    {
        $user = new User;

        $user->setName('name');
        $user->setPhone('+797812983');
        $user->setEmail('test_telegram@gmail.com');
        $user->setPassword('password');

        $event = new ConfigureTelegramEvent($user);
        $subscriber = new ConfigureTelegramEventSubscriber();
        $subscriber->setTelegramAuthToken($event);

        $token = $user->getTelegramAuthToken();
        $this->assertTrue(is_string($token));
    }

    /**
     * @throws \Exception
     */
    public function testSuccessSend()
    {
        $mock = $this->getMockBuilder(TelegramClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('sendTelegram');

        $model = new Telegram();
        $model->setChatId('chatId');
        $model->setMessage('some notification message');

        $chanel = new TelegramChannel($mock);

        $chanel->send($model);
    }

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