<?php declare(strict_types=1);
/**
 * Created 2021-09-27
 * Author Dmitry Kushneriov
 */

namespace App\Helpers;

use \Throwable;
use App\Exception\HttpException;
use App\Exception\ValidationFailedException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

class JsonResponseHelper
{
    public function createErrorException(Throwable $exception): Response
    {
        if (!($exception instanceof HttpException)) {
            $exception = new HttpException($exception);
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

        if (!empty($exception->getHeaders())) {
            $response->headers->add($exception->getHeaders());
        }

        return $response;
    }
}