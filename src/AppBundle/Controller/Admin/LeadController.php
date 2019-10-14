<?php

namespace AppBundle\Controller\Admin;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LeadController extends CRUDController
{
    /**
     * @return RedirectResponse|Response
     *
     * @throws Exception
     */
    public function archiveAction()
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->admin->checkAccess('archive', $object);
        $objectName = $this->admin->toString($object);

        try {
            $this->admin->archive($object);

            if ($this->isXmlHttpRequest()) {
                return $this->renderJson(['result' => 'ok'], 200, []);
            }

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_archive_success',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        } catch (Exception $e) {
            $this->handleModelManagerException($e);

            if ($this->isXmlHttpRequest()) {
                return $this->renderJson(['result' => 'error'], 200, []);
            }

            $this->addFlash(
                'sonata_flash_error',
                $this->trans(
                    'flash_archive_error',
                    ['%name%' => $this->escapeHtml($objectName)],
                    'SonataAdminBundle'
                )
            );
        }

        return $this->redirectTo($object);
    }
}
