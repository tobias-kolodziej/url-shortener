<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {        
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        $statusCode = 500;
        $title = 'Internal Server Error';
        $detail = $exception->getMessage();
        $type = 'about:blank';

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $title = Response::$statusTexts[$statusCode] ?? 'Error';
        }

        if ($exception instanceof NotFoundHttpException && !$detail) {
            $detail = 'The requested resource was not found.';
        }

        $response = new JsonResponse(
            [
                'type'     => $type,
                'title'    => $title,
                'status'   => $statusCode,
                'detail'   => $detail,
                'instance' => $request->getPathInfo(),
            ],
            $statusCode,
            ['Content-Type' => 'application/problem+json']
        );

        $event->setResponse($response);
    }
}
