<?php

namespace App\EventListener;

use App\Enum\FlashMessagesEnum;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HttpExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof HttpExceptionInterface) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        if (!$refererUrl = $request->headers->get('referer')) {
            $refererUrl = '/';
        }
        $response = new RedirectResponse($refererUrl);
        $response->setStatusCode($exception->getStatusCode());
        $response->headers->replace($exception->getHeaders());

        $session->getFlashBag()->add(FlashMessagesEnum::FAIL, $exception->getMessage());
        $event->setResponse($response);
    }
}
