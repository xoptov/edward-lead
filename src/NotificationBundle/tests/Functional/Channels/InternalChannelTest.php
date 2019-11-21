<?php

namespace Tests\Functionsl\Channels;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use NotificationBundle\Channels\InternalChannel;
use NotificationBundle\Entity\Notification;
use NotificationBundle\tests\Functional\BaseFunctional;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class InternalChannelTest extends BaseFunctional
{
    public function testSuccessSend(){

        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $user = new User;

        $user->setName('name');
        $user->setPhone('+797812983');
        $user->setEmail('test@gmail.com');
        $user->setPassword('password');

        $entityManager->persist($user);

        $model = new Notification();

        $randomString = sha1(rand()) ;
        $model->setMessage($randomString);
        $model->setUser($user);

        $chanel =  $this->client->getContainer()->get(InternalChannel::class);

        $chanel->send($model);

        $item = $this->em
            ->getRepository(Notification::class)
            ->findOneBy(['message' => $randomString]);

        $this->assertInstanceOf(Notification::class, $item);

    }
}