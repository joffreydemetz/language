<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language\Tests;

use JDZ\Language\LanguageException;
use PHPUnit\Framework\TestCase;

class LanguageExceptionTest extends TestCase
{
    public function testIsException(): void
    {
        $exception = new LanguageException('Test message');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(LanguageException::class);
        $this->expectExceptionMessage('Test exception');

        throw new LanguageException('Test exception');
    }

    public function testCanBeCaught(): void
    {
        try {
            throw new LanguageException('Caught exception');
        } catch (LanguageException $e) {
            $this->assertEquals('Caught exception', $e->getMessage());
            return;
        }

        $this->fail('Exception was not caught');
    }
}
