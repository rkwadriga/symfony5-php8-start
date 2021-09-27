<?php declare(strict_types=1);
/**
 * Created 2021-05-29
 * Author Dmitry Kushneriov
 */

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class AbstractTestCase extends TestCase
{
    protected function getPrivateProperty(object $object, string $propertyName): mixed
    {
        $property = (new ReflectionClass(get_class($object)))->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    protected function callPrivateMethod(object $object, string $methodName, array $params): mixed
    {
        $method = (new ReflectionClass(get_class($object)))->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $params);
    }
}