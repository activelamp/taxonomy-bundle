<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle;

use ActiveLAMP\Bundle\TaxonomyBundle\DependencyInjection\Compiler\SerializationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ALTaxonomyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SerializationPass());
    }

}
