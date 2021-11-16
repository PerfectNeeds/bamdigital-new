<?php

namespace PN\Bundle\ServiceBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class CommonListener {

    private $em;

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    public function onKernelController(FilterControllerEvent $event) {
        $session = new Session();
        $locale = $event->getRequest()->attributes->get('_locale');

        if (empty($locale)) {
            $locale = 'ar';
        }
        $session->set('_locale', $locale);

    }

}
