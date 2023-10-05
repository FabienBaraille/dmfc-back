<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            $response = new JsonResponse(['message' => 'Vous n\'avez pas la permission d\'accéder à cette page.'], 403);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
