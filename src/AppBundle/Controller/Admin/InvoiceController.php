<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Invoice;
use AppBundle\Event\AccountEvent;
use AppBundle\Event\InvoiceEvent;
use AppBundle\Service\InvoiceManager;
use AppBundle\Form\Type\InvoiceProcessType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends CRUDController
{
    /**
     * @var InvoiceManager
     */
    private $invoiceManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param InvoiceManager           $invoiceManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        InvoiceManager $invoiceManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->invoiceManager = $invoiceManager;
        $this->eventDispatcher = $eventDispatcher;
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

        $objectName = $this->admin->toString($object);

        try {
            $this->invoiceManager->cancel($object);
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
                    $this->invoiceManager->process($data['invoice'], $data['account']);

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_process_success',
                            ['%name%' => $this->escapeHtml($objectName)],
                            'SonataAdminBundle'
                        )
                    );

                    $this->eventDispatcher->dispatch(
                        InvoiceEvent::PROCESSED,
                        new InvoiceEvent($object)
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