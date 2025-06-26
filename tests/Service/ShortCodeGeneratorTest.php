<?php

namespace App\Tests\Service;

use App\Exception\ShortcodeGenerationFailedException;
use App\Service\ShortCodeGenerator;
use PHPUnit\Framework\TestCase;

class ShortCodeGeneratorTest extends TestCase
{
    public function testGenerateReturnsIterableOfCodes(): void
    {
        $shortCodeGenerator = new ShortCodeGenerator();
        $input = 'https://unit-test.com';

        $codes = [];

        try {
            foreach($shortCodeGenerator->generate($input) as $shortCode) {
                $codes[] = $shortCode;
            }
        } catch (\App\Exception\ShortcodeGenerationFailedException $exception) {
            // ignore the ShortcodeGenerationFailedException
        }

        $this->assertNotEmpty($codes);
        $this->assertEquals(
            ['2KJ9yfUCA', '2wa5EhfnV', '2GAW6wHvr', '2LxFefCoe', '24o6ZfnBZ'],
            $codes
        );

        foreach ($codes as $code) {
            $this->assertIsString($code);
            $this->assertGreaterThanOrEqual(8, strlen($code));
        }
    }

    public function testGenerateThrowsExceptionWhenHashIsTooShort(): void
    {
        $shortCodeGenerator = new class extends ShortCodeGenerator {
            public function generate(string $input): iterable
            {
                yield from parent::generate('');
            }
        };

        $this->expectException(ShortcodeGenerationFailedException::class);
        iterator_to_array($shortCodeGenerator->generate(''));
    }
}
