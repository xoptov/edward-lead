<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;

class PhoneCallController extends CRUDController
{
    /**
     * @inheritdoc
     */
    public function preShow(Request $request, $object)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->admin->checkAccess('show', $object);
        $this->admin->setSubject($object);

        $fields = $this->admin->getShow();
        \assert($fields instanceof FieldDescriptionCollection);

        // NEXT_MAJOR: replace deprecation with exception
        if (!\is_array($fields->getElements()) || 0 === $fields->count()) {
            @trigger_error(
                'Calling this method without implementing "configureShowFields"'
                .' is not supported since 3.40.0'
                .' and will no longer be possible in 4.0',
                E_USER_DEPRECATED
            );
        }

        return $this->renderWithExtraParams(
            '@App/CRUD/show_phone_call.html.twig',
            [
                'action' => 'show',
                'object' => $object,
                'elements' => $fields,
            ],
            null
        );
    }
}