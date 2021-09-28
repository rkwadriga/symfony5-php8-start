<?php declare(strict_types=1);
/**
 * Created 2021-05-29
 * Author Dmitry Kushneriov
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiTestCase extends WebTestCase
{
    use ReflectionHelper;

    protected function get(string $uri, array $params = [], array $headers = []): Response
    {
        return $this->request(Request::METHOD_GET, $uri, $params, $headers);
    }

    protected function post(string $uri, array $params = [], array $headers = []): Response
    {
        return $this->request(Request::METHOD_POST, $uri, $params, $headers);
    }

    protected function put(string $uri, array $params = [], array $headers = []): Response
    {
        return $this->request(Request::METHOD_PUT, $uri, $params, $headers);
    }

    protected function delete(string $uri, array $params = [], array $headers = []): Response
    {
        return $this->request(Request::METHOD_DELETE, $uri, $params, $headers);
    }

    protected function request(string $method, string $uri, array $params = [], array $headers = []): Response
    {
        $client = static::createClient();
        $client->jsonRequest($method, $uri, $params, $headers);
        return $client->getResponse();
    }
}