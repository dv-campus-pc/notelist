<?php

namespace App\EventListener;

use App\Enum\FlashMessagesEnum;
use App\Exception\ValidationException;
use App\Service\DataTransformService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class HttpExceptionListener
{
    private DataTransformService $dataTransformService;

    public function __construct(DataTransformService $dataTransformService) {
        $this->dataTransformService = $dataTransformService;
    }
    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getRequestUri(), '/api/') === 0) {
            $this->handleApiException($event);
            return;
        }

        $exception = $event->getThrowable();
        if (!$exception instanceof ValidationException) {
            return;
        }

        $session = $request->getSession();
        if (!$refererUrl = $request->headers->get('referer')) {
            $refererUrl = '/';
        }
        $response = new RedirectResponse($refererUrl);
        $response->setStatusCode($exception->getCode());

        foreach ($exception->getErrorsList() as $error) {
            $session->getFlashBag()->add(FlashMessagesEnum::FAIL, $error->getMessage());
        }

        if ($exception->getMessage()) {
            $session->getFlashBag()->add(FlashMessagesEnum::FAIL, $exception->getMessage());
        }

        $event->setResponse($response);
    }

    private function handleApiException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $event->setResponse(new JsonResponse(
            ['errors' => $this->getErrorMessages($exception)],
            $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR
        ));
    }

    private function getErrorMessages(Throwable $exception): array
    {
        if ($exception instanceof ValidationException) {
            return $exception->getErrorsList()
                ? $this->dataTransformService->transformViolationListToArray($exception->getErrorsList())
                : [$exception->getMessage()];
        }

        if ($exception instanceof NotFoundHttpException) {
            return ['Entity not found in the database'];
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return ['Access denied'];
        }

        return ['Something went wrong ... Try later'];
    }
}
