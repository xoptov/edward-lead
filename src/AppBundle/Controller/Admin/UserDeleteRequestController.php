<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\UserDeleteRequest;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserDeleteRequestController extends CRUDController
{
    /**
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function acceptAction(int $id)
    {
        /** @var UserDeleteRequest $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Не найден объект с казанным идентификатором: %s', $id));
        }

        if (!$object instanceof UserDeleteRequest) {
            throw new \InvalidArgumentException('Данный тип объекта не поддерживается');
        }

        if ($object->isProcessed()) {
            throw new \Exception('Сделка уже обработана');
        }

        $objectName = $this->admin->toString($object);

        try {
            $user = $object->getUser();
            $user->setEnabled(false);
            $object->setStatus(UserDeleteRequest::STATUS_ACCEPTED);
            $this->admin->update($object);

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_accept_success',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        } catch (ModelManagerException $e) {
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
        /** @var UserDeleteRequest $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Не найден объект с казанным идентификатором: %s', $id));
        }

        if (!$object instanceof UserDeleteRequest) {
            throw new \InvalidArgumentException('Данный тип объекта не поддерживается');
        }

        if ($object->isProcessed()) {
            throw new \Exception('Сделка уже обработана');
        }

        $objectName = $this->admin->toString($object);

        try {
            $object->setStatus(UserDeleteRequest::STATUS_REJECTED);
            $this->admin->update($object);

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_reject_success',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);

            $this->addFlash(
                'sonata_flash_error',
                $this->trans(
                    'flash_reject_error',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}