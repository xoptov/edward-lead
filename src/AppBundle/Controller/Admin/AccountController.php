<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Account;
use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\MonetaryHold;
use AppBundle\Entity\MonetaryTransaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccountController extends CRUDController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

    /**
     * Show action.
     *
     * @param int|string|null $id
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     *
     * @return Response
     */
    public function showAction($id = null)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->admin->checkAccess('show', $object);

        $fields = $this->admin->getShow();
        \assert($fields instanceof FieldDescriptionCollection);

        // NEXT_MAJOR: replace deprecation with exception
        if (!\is_array($fields->getElements()) || 0 === $fields->count()) {
            @trigger_error(
                'Calling this method without implementing "configureShowFields"'
                .' is not supported since 3.x'
                .' and will no longer be possible in 4.0',
                E_USER_DEPRECATED
            );
        }

        $transactions = $this->entityManager
            ->getRepository(MonetaryTransaction::class)
            ->findBy(['account' => $object]);

        $holds = [];

        if ($object instanceof ClientAccount) {
            $holds = $this->entityManager
                ->getRepository(MonetaryHold::class)
                ->findBy(['account' => $object]);
        }

        return $this->renderWithExtraParams('@App/CRUD/show_account.html.twig', [
            'action' => 'show',
            'object' => $object,
            'elements' => $fields,
            'transactions' => $transactions,
            'holds' => $holds
        ], null);
    }
}