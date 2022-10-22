<?php

use JsonSchema\Uri\UriRetriever;
use JsonSchemaForm\Generator;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class GeneratorUnitTest extends TestCase
{
    private Generator $SUT;

    public function setUp(): void
    {
        $schema = (new UriRetriever())->retrieve('file://' . realpath(dirname(__FILE__) . '/../fixtures/schema.json'));
        $this->SUT = new Generator($schema);
    }

    public function testCanGenerateTwigEnvironment()
    {
        self::assertInstanceOf(Environment::class, $this->SUT->getDefaultTwigEnvironment());
    }
}
