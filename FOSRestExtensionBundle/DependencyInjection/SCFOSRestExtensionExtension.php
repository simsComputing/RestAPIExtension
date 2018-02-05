<?php
namespace SC\FOSRestExtensionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class SCFOSRestExtensionExtension extends Extension
{

    /**
     *
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load("api_connection_processor.yml");
        $loader->load("patch_processor.yml");
        
        $this->loadQueryFiltersEntities($config, $container);
        $this->loadAPIConnectionProcessorRequirements($config, $container);
        $this->loadPatchProcessorBans($config, $container);
    }

    protected function loadQueryFiltersEntities(array $config, ContainerInterface $container)
    {
        foreach ($config["query_filters"] as $entity) {
            $container->setParameter("scfos_rest_extension.query_filters." . $entity["class"], $entity["fields"]);
        }
    }
    
    protected function loadPatchProcessorBans(array $config, ContainerInterface $container) {
        $container->setParameter("scfos_rest_extension.patch_processor.banning", $config["patch_processor"]["banning"]);
    }

    protected function loadAPIConnectionProcessorRequirements(array $config, ContainerInterface $container)
    {
        $processor_config = $config["api_connection_processor"];
        $methods = $processor_config["methods"];
        $custom_methods = $config["api_connection_processor"]["custom_methods"];
        foreach ($custom_methods as $method) {
            $methods[$method] = true;
        }
        $container->setParameter("scfos_rest_extension.api_connection_processor.methods", $methods);
        $container->setParameter("scfos_rest_extension.api_connection_processor.pattern", $processor_config["pattern"]);
    }
}