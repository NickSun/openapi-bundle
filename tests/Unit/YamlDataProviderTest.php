<?php

namespace Tests\Unit;

use NickSun\OpenApi\Service\YamlDataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

final class YamlDataProviderTest extends TestCase
{
    private ParameterBagInterface $params;

    protected function setUp(): void
    {
        $this->params = $this->createMock(ParameterBagInterface::class);
        $this->params
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                ['kernel.project_dir'],
                ['open_api.definitions_dir'],
            )
            ->willReturnOnConsecutiveCalls(
                __DIR__ . '/../fixtures',
                'openapi',
            );
    }

    /**
     * @see YamlDataProvider::getDefinitions()
     */
    public function testGetDefinitions(): void
    {
        $yamlDataProvider = new YamlDataProvider($this->params);
        $yamlDefinitions = $yamlDataProvider->getDefinitions();

        $yamlExpected = Yaml::parseFile(__DIR__ . '/../fixtures/expected.yaml');

        $this->assertEquals($yamlExpected, $yamlDefinitions);
    }
}
