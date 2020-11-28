<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SelectRoleSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FirewallMap
     */
    private $firewallMap;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 4]
        ];
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface       $router
     * @param FirewallMap           $firewallMap
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        FirewallMap $firewallMap
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->firewallMap = $firewallMap;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $config = $this->firewallMap->getFirewallConfig($request);

        if ($config->getContext() !== 'main') {
            return;
        }

        $uri = $request->getRequestUri();

        $excludedPaths = [
            $this->router->generate('app_user_select_role'),
            $this->router->generate('app_user_stay_advertiser'),
            $this->router->generate('app_user_stay_webmaster')
        ];

        if (in_array($uri, $excludedPaths)) {
            return;
        }

        if (preg_match('/^\/(?:api|media\/cache)/', $uri)) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return;
        }

        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
             return;
        }

        if (!$user->isRoleSelected()) {
            $redirectUrl = $this->router->generate('app_user_select_role');
            $response = new RedirectResponse($redirectUrl);

            $event->setResponse($response);
        }
    }
}