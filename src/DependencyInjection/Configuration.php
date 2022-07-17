<?php

namespace NickSun\OpenApi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('openapi');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->variableNode('definitions_dir')->defaultValue('openapi')->end()
                ?->variableNode('swagger_ui_version')->defaultValue('3.46.0')->end()
                ?->variableNode('title')->defaultValue('Swagger UI')->end()
            ?->end();

        return $treeBuilder;
    }
}
