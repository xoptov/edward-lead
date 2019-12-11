<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class NotificationAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'notification/mass';
    protected $baseRouteName = 'notification_mass_send';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);
    }
}