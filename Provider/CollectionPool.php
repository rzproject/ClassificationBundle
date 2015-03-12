<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\CoreBundle\Validator\ErrorElement;

class CollectionPool extends Pool
{

    /**
     * @param string                 $name
     * @param CollectionProviderInterface $instance
     *
     * @return void
     */
    public function addProvider($name, CollectionProviderInterface $instance)
    {
        $this->providers[$name] = $instance;
    }

    /**
     * @param \Sonata\CoreBundle\Validator\ErrorElement $errorElement
     * @param \Sonata\ClassificationBundle\Model\CollectionInterface   $collection
     *
     * @return void
     */
    public function validate(ErrorElement $errorElement, CollectionInterface $collection)
    {
    }
}
