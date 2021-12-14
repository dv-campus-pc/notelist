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
        $request = $event->getRequest();
        $session = $request->getSession();
        if (!$refererUrl = $request->headers->get('referer')) {
            $refererUrl = '/';
        }
        $response = new RedirectResponse($refererUrl);

        if ($exception instanceof HttpExceptionInterface) {
            $failureMessage = $exception->getMessage();
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $failureMessage = 'Internal server error. Please contact website admin.';
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $session->getFlashBag()->add(FlashMessagesEnum::FAIL, $failureMessage);
        $event->setResponse($response);
    }
}
