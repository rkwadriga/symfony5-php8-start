<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\EventSubscriber;

use App\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Exception\HttpException;
use App\Exception\AuthException;

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

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'handleException',
            KernelEvents::RESPONSE => 'handleResponse',
        ];
    }

    public function handleException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AuthException) {
            $exception = new HttpException($exception);
        } elseif (!($exception instanceof HttpException)) {
            return;
        }

        $code = $exception->getCode();
        $context = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        if (($previous = $exception->getPrevious()) !== null) {
            if ($previous instanceof ValidationFailedException) {
                $code = 'VALIDATION_ERROR';
                $context = [
                    'fields' => [],
                ];
                foreach ($previous->getViolations() as $violation) {
                    if (!($violation instanceof ConstraintViolation)) {
                        continue;
                    }
                    $context['fields'][$violation->getPropertyPath()] = [
                        'value' => $violation->getInvalidValue(),
                        'error' => $violation->getMessage(),
                        'code' => $violation->getCode() === UniqueEntity::NOT_UNIQUE_ERROR ? 'NOT_UNIQUE_ERROR' : $violation->getCode(),
                    ];
                }
            } else {
                $context['file'] = $previous->getFile();
                $context['line'] = $previous->getLine();
            }
        }

        $data = [
            'message' => $exception->getMessage(),
            'code' => $code,
            'context' => $context,
        ];
        $response = new JsonResponse(['error' => $data], $exception->getStatusCode());

        $response->headers->add($this->createResponseHeaders());
        if (!empty($exception->getHeaders())) {
            $response->headers->add($exception->getHeaders());
        }
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