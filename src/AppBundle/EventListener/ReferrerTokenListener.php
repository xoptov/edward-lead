<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReferrerTokenListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 32]
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $referrer = $request->query->get('ref');

        if (empty($referrer)) {
            return;
        }

        if ($request->cookies->has('referrer')
            && $request->cookies->get('referrer') == $referrer
        ) {
            return;
        }

        $cookie = new Cookie('referrer', $referrer, new \DateTime('+1 week'));

        $response = $event->getResponse();
        $response->headers->setCookie($cookie);
    }
}