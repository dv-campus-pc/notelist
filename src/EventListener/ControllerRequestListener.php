<?php

namespace App\EventListener;

use App\Service\ActivityService;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ControllerRequestListener
{
    private ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function onControllerRequest(ResponseEvent $event)
    {
        $this->activityService->createFromRequestResponse(
            $event->getRequest(),
            $event->getResponse()
        );
    }
}
