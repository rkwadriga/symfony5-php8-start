<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException as BaseHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class HttpException extends BaseHttpException
{
    protected $message = 'Something went wrong!';

    public function __construct(\Throwable $exception, ?string $message = null, ?int $statusCode = null, array $headers = [])
    {
        if ($message === null) {
            $message = $exception->getMessage();
        }
        if ($statusCode === null) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($exception instanceof ValidationFailedException) {
                $statusCode = Response::HTTP_BAD_REQUEST;
            } elseif ($exception instanceof AuthException) {
                $statusCode = $exception->getCode();
            } elseif ($exception instanceof BaseHttpException) {
                $statusCode = $exception->getStatusCode();
            } elseif ($exception instanceof AuthenticationException) {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        parent::__construct($statusCode, $message, $exception, $headers, $exception->getCode());
    }
}