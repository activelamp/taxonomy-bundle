<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\DependencyInjection;

use ActiveLAMP\Bundle\TaxonomyBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ALTaxonomyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('forms.yml');

        $bundles = $container->getParameter('kernel.bundles');

        $files = array();
        foreach ($bundles as $name => $ns) {
            $ref = new \ReflectionClass($ns);
            $file = dirname($ref->getFileName()) . '/Resources/config/taxonomy.yml';
            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        $definition = $container->getDefinition('al_taxonomy.taxonomy_loader');
        $definition->replaceArgument(0, $files);

    }
}
