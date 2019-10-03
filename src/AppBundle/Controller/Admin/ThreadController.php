<?php

namespace AppBundle\Controller\Admin;

use Twig\Environment;
use Twig\Error\RuntimeError;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Message;
use AppBundle\Form\Type\AdminThreadType;
use FOS\MessageBundle\Composer\Composer;
use Symfony\Component\Form\FormRenderer;
use AppBundle\Form\Type\AdminMessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;

class ThreadController extends CRUDController
{
    /**
     * @return Response
     *
     * @throws RuntimeError
     */
    public function replyAction()
    {
        $request = $this->getRequest();

        $id = $request->get($this->admin->getIdParameter());
        /** @var Thread $existingObject */
        $existingObject = $this->admin->getObject($id);

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->admin->setSubject($existingObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $message = new Message();
        $message->setThread($existingObject);
        $form = $this->createForm(AdminMessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {

                $composer = $this->get('fos_message.composer');
                /** @var Message $newMessage */
                $newMessage = $form->getData();

                $message = $composer->reply($existingObject)
                    ->setSender($this->getUser())
                    ->setBody($newMessage->getBody())
                    ->getMessage();

                $existingObject->setStatus(Thread::STATUS_WAIT_USER);

                try {
                    $sender = $this->get('fos_message.sender');
                    $sender->send($message);

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_reply_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'message'
                        )
                    );

                    return $this->redirectTo($existingObject);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', [
                        '%name%' => $this->escapeHtml($this->admin->toString($existingObject)),
                        '%link_start%' => '<a href="'.$this->admin->generateObjectUrl('edit', $existingObject).'">',
                        '%link_end%' => '</a>',
                    ], 'SonataAdminBundle'));
                }
            }

            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_reply_error',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'message'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        /** @var Environment $twig */
        $twig = $this->get('twig');
        $twig->getRuntime(FormRenderer::class)->setTheme($formView, '@SonataAdmin/Form/form_admin_fields.html.twig');

        return $this->renderWithExtraParams('@App/CRUD/thread_reply.html.twig', [
            'action' => 'edit',
            'form' => $formView,
            'object' => $existingObject,
            'objectId' => $objectId,
        ], null);
    }

    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     *
     * @throws RuntimeError]
     */
    public function writeToSellerAction($id = null)
    {
        $request = $this->getRequest();

        $id = $request->get($this->admin->getIdParameter());
        /** @var Thread $existingObject */
        $existingObject = $this->admin->getObject($id);

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        /** @var Composer $composer */
        $composer = $this->get('fos_message.composer');

        /** @var Message $message */
        $message = $composer->newThread()
            ->setSender($this->getUser())
            ->addRecipient($existingObject->getLead()->getUser())
            ->setSubject('')
            ->setBody('')
            ->getMessage();

        /** @var Thread $newObject */
        $newObject = $message->getThread();

        $this->admin->setSubject($newObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $form = $this->createForm(AdminThreadType::class, $newObject);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            if ($isFormValid) {

                try {
                    /** @var Thread $thread */
                    $thread = $message->getThread();

                    $thread->setStatus(Thread::STATUS_NEW);
                    $thread->setTypeAppeal(Thread::TYPE_ARBITRATION);
                    $thread->setLead($existingObject->getLead());

                    $sender = $this->get('fos_message.sender');
                    $sender->send($message);

                    $existingObject->setSellerThread($thread);

                    $this->getDoctrine()->getManager()->flush();

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_create_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                            'SonataAdminBundle'
                        )
                    );

                } catch (\Exception $e) {
                    $isFormValid = false;

                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_create_error',
                            ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                            'SonataAdminBundle'
                        )
                    );
                }
            }

            if ($isFormValid && is_null($request->get('btn_update_and_edit'))) {
                return $this->redirectTo($existingObject);
            } else {
                return $this->redirectToList();
            }
        }

        $formView = $form->createView();
        /** @var Environment $twig */
        $twig = $this->get('twig');
        $twig->getRuntime(FormRenderer::class)->setTheme($formView, '@SonataAdmin/Form/form_admin_fields.html.twig');

        return $this->renderWithExtraParams('@App/CRUD/thread_create.html.twig', [
            'action' => 'edit',
            'form' => $formView,
            'object' => $existingObject,
            'objectId' => $objectId,
        ], null);
    }

    /**
     * @param int|null $id
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function closeAction($id = null)
    {
        $request = $this->getRequest();

        $id = $request->get($this->admin->getIdParameter());
        /** @var Thread $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->admin->checkAccess('edit', $object);

        /** @var Thread $childObject */
        $childObject = $object->getSellerThread();

        $object->setStatus(Thread::STATUS_CLOSED);
        if ($childObject) {
            $childObject->setStatus(Thread::STATUS_CLOSED);
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_edit_success',
                    ['%name%' => $this->escapeHtml($this->admin->toString($object))],
                    'SonataAdminBundle'
                )
            );
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
        } catch (LockException $e) {
            $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', [
                '%name%' => $this->escapeHtml($this->admin->toString($object)),
                '%link_start%' => '<a href="'.$this->admin->generateObjectUrl('edit', $object).'">',
                '%link_end%' => '</a>',
            ], 'SonataAdminBundle'));
        }

        return $this->redirectToList();
    }

    public function preShow(Request $request, $object)
    {
        $this->admin->setSubject($object);

        $fields = $this->admin->getShow();
        \assert($fields instanceof FieldDescriptionCollection);

        if (!\is_array($fields->getElements()) || 0 === $fields->count()) {
            @trigger_error(
                'Calling this method without implementing "configureShowFields"'
                .' is not supported since 3.40.0'
                .' and will no longer be possible in 4.0',
                E_USER_DEPRECATED
            );
        }

        return $this->renderWithExtraParams('@App/CRUD/show_thread.html.twig', [
            'action' => 'show',
            'object' => $object,
            'elements' => $fields,
        ], null);
    }
}