<?php

namespace App\EventListener;

use App\Enum\FlashMessagesEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HttpExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getRequestUri(), '/api/') === 0) {
            $this->handleApiException($event);
            return;
        }

        $exception = $event->getThrowable();
        if (!$exception instanceof HttpExceptionInterface) {
            return;
        }

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

    private function handleApiException(ExceptionEvent $event): void {
        $exception = $event->getThrowable();
        $message = $exception instanceof HttpExceptionInterface
            ? $exception->getMessage()
            : 'Something went wrong ... Try later';

        $event->setResponse(new JsonResponse(['error' => $message]));
    }
}
