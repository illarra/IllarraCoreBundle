<?php

namespace Illarra\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class AdminLocaleListener
{
    private $locale;

    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        // If we're under /admin* set a default locale
        if (strpos($event->getRequest()->getPathInfo(), '/admin') === 0) {
            $event->getRequest()->setLocale($this->locale);
        }
    }
}