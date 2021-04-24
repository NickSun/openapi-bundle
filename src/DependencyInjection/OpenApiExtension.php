<?php

namespace NickSun\OpenApi\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class OpenApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('open_api.definitions_dir', $config['definitions_dir'] ?? 'openapi');
        $container->setParameter('open_api.swagger_ui_version', $config['swagger_ui_version'] ?? '3.46.0');
        $container->setParameter('open_api.title', $config['title'] ?? 'Swagger UI');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
