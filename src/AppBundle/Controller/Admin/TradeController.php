<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Trade;
use AppBundle\Service\TradeManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TradeController extends CRUDController
{
    /**
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @param TradeManager $tradeManager
     */
    public function __construct(TradeManager $tradeManager)
    {
        $this->tradeManager = $tradeManager;
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
            $this->tradeManager->finishReject($object);
            $this->admin->update($object);

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_cancel_success',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        } catch (ModelManagerException $e) {
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