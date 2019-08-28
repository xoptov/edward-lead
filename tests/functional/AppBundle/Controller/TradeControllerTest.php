<?php

namespace functional\AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\MonetaryHold;
use AppBundle\Service\RoomManager;
use AppBundle\Service\UserManager;
use AppBundle\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase
{
    public function testRejectAction_withLeadReservedInRoomWithoutWarranty()
    {
        $client = $this->createClient([], [
            'PHP_AUTH_USER' => 'company@test.ru',
            'PHP_AUTH_PW' => 123456
        ]);

        $doctrine = $client->getContainer()->get('doctrine');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $doctrine->getManager();
        $entityManager->beginTransaction();

        /** @var UserManager $userManager */
        $userManager = $client->getContainer()->get(UserManager::class);

        $webmasterUser = new User();
        $webmasterUser
            ->setEmail('webmaster@test.ru')
            ->setPassword('test')
            ->setRoles(['ROLE_WEBMASTER'])
            ->setEnabled(true)
            ->setName('Webmaster')
            ->setPhone('79000000000')
            ->makeTypeSelected();

        $entityManager->persist($webmasterUser);

        $webmasterAccount = new ClientAccount();
        $webmasterAccount
            ->setUser($webmasterUser)
            ->setEnabled(true);

        $entityManager->persist($webmasterAccount);

        $companyUser = new User();
        $companyUser
            ->setEmail('company@test.ru')
            ->setPlainPassword(123456)
            ->setRoles(['ROLE_COMPANY'])
            ->setEnabled(true)
            ->setName('Company')
            ->setPhone('79000000001')
            ->makeTypeSelected();

        $entityManager->persist($companyUser);
        $userManager->updateUser($companyUser, false);

        $companyAccount = new ClientAccount();
        $companyAccount
            ->setUser($companyUser)
            ->setEnabled(true);

        $entityManager->persist($companyAccount);

        /** @var RoomManager $roomManager */
        $roomManager = $client->getContainer()->get(RoomManager::class);

        $room = new Room();
        $room
            ->setName('Тестовая комната')
            ->setOwner($webmasterUser)
            ->setSphere('Любая')
            ->setInviteToken('test')
            ->setPlatformWarranty(false)
            ->setEnabled(true);

        $entityManager->persist($room);

        $roomManager->joinInRoom($room, $webmasterUser);
        $roomManager->joinInRoom($room, $companyUser);

        $lead = new Lead();
        $lead
            ->setUser($webmasterUser)
            ->setRoom($room)
            ->setPhone('79000000002')
            ->setExpirationDate(new \DateTime('+2 day'))
            ->setPrice(10000)
            ->setStatus(Lead::STATUS_RESERVED);

        $entityManager->persist($lead);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setSeller($webmasterUser)
            ->setBuyer($companyUser)
            ->setAmount(10000);

        $entityManager->persist($trade);

        $tradeHold = new MonetaryHold();
        $tradeHold
            ->setAmount(1000)
            ->setOperation($trade)
            ->setAccount($companyAccount);

        $trade->setHold($tradeHold);

        $entityManager->persist($tradeHold);
        $entityManager->flush();

        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('app_trade_reject', ['id' => $trade->getId()]));

        $response = $client->getResponse();

        $redirectTargetUrl = $router->generate('app_lead_show', ['id' => $lead->getId()]);

        $this->assertTrue($response->isRedirect($redirectTargetUrl));

        $this->assertTrue($trade->isProcessed());
        $this->assertEquals(Trade::STATUS_REJECTED, $trade->getStatus());
        $this->assertEquals(Lead::STATUS_BLOCKED, $lead->getStatus());

        $entityManager->rollback();
    }

    public function testRejectAction_withLeadReservedInRoomWithWarranty()
    {
        $client = $this->createClient([], [
            'PHP_AUTH_USER' => 'company@test.ru',
            'PHP_AUTH_PW' => 123456
        ]);

        $doctrine = $client->getContainer()->get('doctrine');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $doctrine->getManager();
        $entityManager->beginTransaction();

        /** @var UserManager $userManager */
        $userManager = $client->getContainer()->get(UserManager::class);

        $webmasterUser = new User();
        $webmasterUser
            ->setEmail('webmaster@test.ru')
            ->setPassword('test')
            ->setRoles(['ROLE_WEBMASTER'])
            ->setEnabled(true)
            ->setName('Webmaster')
            ->setPhone('79000000000')
            ->makeTypeSelected();

        $entityManager->persist($webmasterUser);

        $webmasterAccount = new ClientAccount();
        $webmasterAccount
            ->setUser($webmasterUser)
            ->setEnabled(true);

        $entityManager->persist($webmasterAccount);

        $companyUser = new User();
        $companyUser
            ->setEmail('company@test.ru')
            ->setPlainPassword(123456)
            ->setRoles(['ROLE_COMPANY'])
            ->setEnabled(true)
            ->setName('Company')
            ->setPhone('79000000001')
            ->makeTypeSelected();

        $entityManager->persist($companyUser);
        $userManager->updateUser($companyUser, false);

        $companyAccount = new ClientAccount();
        $companyAccount
            ->setUser($companyUser)
            ->setEnabled(true);

        $entityManager->persist($companyAccount);

        /** @var RoomManager $roomManager */
        $roomManager = $client->getContainer()->get(RoomManager::class);

        $room = new Room();
        $room
            ->setName('Тестовая комната')
            ->setOwner($webmasterUser)
            ->setSphere('Любая')
            ->setInviteToken('test')
            ->setPlatformWarranty(true)
            ->setEnabled(true);

        $entityManager->persist($room);

        $roomManager->joinInRoom($room, $webmasterUser);
        $roomManager->joinInRoom($room, $companyUser);

        $lead = new Lead();
        $lead
            ->setUser($webmasterUser)
            ->setRoom($room)
            ->setPhone('79000000002')
            ->setExpirationDate(new \DateTime('+2 day'))
            ->setPrice(10000)
            ->setStatus(Lead::STATUS_RESERVED);

        $entityManager->persist($lead);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setSeller($webmasterUser)
            ->setBuyer($companyUser)
            ->setAmount(10000);

        $entityManager->persist($trade);

        $tradeHold = new MonetaryHold();
        $tradeHold
            ->setAmount(1000)
            ->setOperation($trade)
            ->setAccount($companyAccount);

        $trade->setHold($tradeHold);

        $entityManager->persist($tradeHold);
        $entityManager->flush();

        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('app_trade_reject', ['id' => $trade->getId()]));

        $response = $client->getResponse();

        $redirectTargetUrl = $router->generate('app_lead_show', ['id' => $lead->getId()]);

        $this->assertTrue($response->isRedirect($redirectTargetUrl));

        $this->assertFalse($trade->isProcessed());
        $this->assertEquals(Trade::STATUS_ARBITRAGE, $trade->getStatus());
        $this->assertEquals(Lead::STATUS_NO_TARGET, $lead->getStatus());

        $entityManager->rollback();
    }
}