<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 1:47 PM
 */

namespace ActiveLAMP\TaxonomyBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SerializationPass
 *
 * @package ActiveLAMP\TaxonomyBundle\DependencyInjection\Compiler
 * @author Bez Hermoso <bez@activelamp.com>
 */
class SerializationPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this->compileJMS($container);
    }

    private function compileJMS(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jms_serializer.serializer')) {
            return;
        }

        $handler =
            new Definition(
                'ActiveLAMP\\TaxonomyBundle\\Serializer\\Handler\\PluralVocabularyFieldHandler',
                array(
                    new Reference('service_container'),
                )
            );

        $handler->addTag('jms_serializer.subscribing_handler');
        $container->setDefinition('al_taxonomy.jms_serializer.handler.plural_vocabulary_field', $handler);

        $handler =
            new Definition(
                'ActiveLAMP\\TaxonomyBundle\\Serializer\\Handler\\SingularVocabularyFieldHandler',
                array(
                    new Reference('service_container'),
                )
            );
        $handler->addTag('jms_serializer.subscribing_handler');
        $container->setDefinition('al_taxonomy.jms_serializer.handler.singular_vocabulary_field', $handler);

    }
}