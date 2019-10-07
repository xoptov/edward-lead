<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Event\TradeEvent;
use AppBundle\Service\TradeManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TradeController extends CRUDController
{
    /**
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param TradeManager             $tradeManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        TradeManager $tradeManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->tradeManager = $tradeManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function acceptAction(int $id)
    {
        /** @var Trade $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Не найден объект с казанным идентификатором: %s', $id));
        }

        if (!$object instanceof Trade) {
            throw new \InvalidArgumentException('Данный тип объекта не поддерживается');
        }

        if ($object->isProcessed()) {
            throw new \Exception('Сделка уже обработана');
        }

        $objectName = $this->admin->toString($object);

        try {

            $feesAccount = $this->getDoctrine()->getRepository(Account::class)
                ->getFeesAccount();

            $this->tradeManager->accept($object, $feesAccount);
            $this->admin->update($object);

            $this->eventDispatcher->dispatch(Trade::STATUS_ACCEPTED, new TradeEvent($object));

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_accept_success',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        } catch (\Exception $e) {
            $this->handleModelManagerException($e);

            $this->addFlash(
                'sonata_flash_error',
                $this->trans(
                    'flash_accept_error',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function rejectAction(int $id)
    {
        /** @var Trade $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Не найден объект с казанным идентификатором: %s', $id));
        }

        if (!$object instanceof Trade) {
            throw new \InvalidArgumentException('Данный тип объекта не поддерживается');
        }

        if ($object->isProcessed()) {
            throw new \Exception('Сделка уже обработана');
        }

        $objectName = $this->admin->toString($object);

        try {
            $this->tradeManager->reject($object);
            $this->admin->update($object);

            $this->eventDispatcher->dispatch(Trade::STATUS_REJECTED, new TradeEvent($object));

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_cancel_success',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        } catch (\Exception $e) {
            $this->handleModelManagerException($e);

            $this->addFlash(
                'sonata_flash_error',
                $this->trans(
                    'flash_cancel_error',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}