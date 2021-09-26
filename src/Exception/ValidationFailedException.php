<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\Exception;

use Symfony\Component\Validator\Exception\ValidationFailedException as BaseException;

class ValidationFailedException extends BaseException
{

}