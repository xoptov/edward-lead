<?php

namespace NotificationBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;

class NotificationController extends CRUDController
{
    public function listAction()
    {
        return $this->renderWithExtraParams('@NotificationBundle/admin/notification.html.twig');
    }
}
