<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\EventSubscriber;

use App\Helpers\JsonResponseHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AfterActionSubscriber implements EventSubscriberInterface
{
    private array $_allowedHeaders = [
        'X-AUTH-TOKEN',
        'DNT',
        'Keep-Alive',
        'User-Agent',
        'X-Requested-With',
        'X-Debug-Token',
        'X-Debug-Token-Link',
        'If-Modified-Since',
        'Cache-Control',
        'Content-Type',
        'Content-Length',
    ];

    private array $_allowedMethods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'OPTIONS',
    ];

    public function __construct(
        private JsonResponseHelper $jsonResponseHelper
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'handleException',
            KernelEvents::RESPONSE => 'handleResponse',
        ];
    }

    public function handleException(ExceptionEvent $event): void
    {
        $response = $this->jsonResponseHelper->createErrorException($event->getThrowable());

        $response->headers->add($this->createResponseHeaders());
        $event->setResponse($response);
    }

    public function handleResponse(ResponseEvent $event): void
    {
        $event->getResponse()->headers->add($this->createResponseHeaders());
        if ($event->getRequest()->getMethod() === Request::METHOD_OPTIONS) {
            $event->getResponse()->setStatusCode(Response::HTTP_NO_CONTENT);
        }
    }

    private function createResponseHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => implode(', ', $this->_allowedMethods),
            'Access-Control-Allow-Headers' => implode(', ', $this->_allowedHeaders),
            'Access-Control-Max-Age' => 1728000,
            'Content-Type' => 'text/plain charset=UTF-8',
            'Content-Length' => 0
        ];
    }
}