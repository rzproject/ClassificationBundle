<?php

namespace Rz\ClassificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Rz\ClassificationBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;
use Rz\ClassificationBundle\DependencyInjection\Compiler\AddProviderCompilerPass;

class RzClassificationBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverrideServiceCompilerPass());
        $container->addCompilerPass(new AddProviderCompilerPass());
    }
}
