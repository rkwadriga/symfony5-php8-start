<?php
/**
 * Created 2021-09-28
 * Author Dmitry Kushneriov
 */

namespace App\Tests;

use ReflectionClass;

trait ReflectionHelper
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