<?php declare(strict_types=1);
/**
 * Created 2021-05-29
 * Author Dmitry Kushneriov
 */

namespace Tests;

/**
 * Command ./run DefaultTest
 */
class DefaultTest extends AbstractTest
{
    public function testHelloWorld(): void
    {
        $this->assertNull(null);
    }
}