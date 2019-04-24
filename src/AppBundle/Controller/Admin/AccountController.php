<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Account;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountController extends CRUDController
{
    /**
     * @param int $id
     *
     * @throws \Exception
     *
     * @return RedirectResponse
     */
    public function toggleAction(int $id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Не найден объект с казанным идентификатором: %s', $id));
        }

        if (!$object instanceof Account) {
            throw new \InvalidArgumentException('Данный тип объекта не поддерживается');
        }

        $objectName = $this->admin->toString($object);

        if ($object->isEnabled()) {
            $object->setEnabled(false);
        } else {
            $object->setEnabled(true);
        }

        try {
            $this->admin->update($object);

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_enable_success',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);

            $this->addFlash(
                'sonata_flash_error',
                $this->trans(
                    'flash_enable_error',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}