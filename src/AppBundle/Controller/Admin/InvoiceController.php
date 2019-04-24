<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Invoice;
use AppBundle\Service\InvoiceManager;
use AppBundle\Form\Type\InvoiceProcessType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends CRUDController
{
    /**
     * @var InvoiceManager
     */
    private $invoiceManager;

    /**
     * @param InvoiceManager $invoiceManager
     */
    public function __construct(InvoiceManager $invoiceManager)
    {
        $this->invoiceManager = $invoiceManager;
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     *
     * @return RedirectResponse
     */
    public function cancelAction(int $id)
    {

        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Не найден объект с казанным идентификатором: %s', $id));
        }

        if (!$object instanceof Invoice) {
            throw new \InvalidArgumentException('Данный тип объекта не поддерживается');
        }

        if ($object->isProcessed()) {
            throw new \Exception('Инвойс уже обработан');
        }

        $object->setStatus(Invoice::STATUS_CANCELED);
        $objectName = $this->admin->toString($object);

        try {
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

    /**
     * @param int $id
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function processAction(int $id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Не найден объект с казанным идентификатором: %s', $id));
        }

        if (!$object instanceof Invoice) {
            throw new \InvalidArgumentException('Данный тип объекта не поддерживается');
        }

        if ($object->isProcessed()) {
            throw new \Exception('Инвойс уже обработан');
        }

        $form = $this->createForm(InvoiceProcessType::class, ['invoice' => $object]);
        $objectName = $this->admin->toString($object);

        if (Request::METHOD_POST === $this->getRestMethod()) {
            $form->handleRequest($this->getRequest());

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $this->invoiceManager->process($data['invoice'], $data['incomeAccount']);

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_process_success',
                            ['%name%' => $this->escapeHtml($objectName)],
                            'SonataAdminBundle'
                        )
                    );
                } catch(\Exception $e) {
                    $this->handleModelManagerException($e);

                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_process_error',
                            ['%name%' => $this->escapeHtml($objectName)],
                            'SonataAdminBundle'
                        )
                    );
                }

                return new RedirectResponse($this->admin->generateUrl('list'));
            }
        }

        return $this->renderWithExtraParams('@App/CRUD/process_operation.html.twig', [
            'action' => 'process',
            'title' => 'title_process_invoice',
            'message' => 'message_process_invoice',
            'form' => $form->createView()
        ]);
    }
}