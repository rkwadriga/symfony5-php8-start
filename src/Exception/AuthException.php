<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\Exception;

class AuthException extends \Exception
{
    public string $name = 'Authentication failed';
}