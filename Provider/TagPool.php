<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\AdminBundle\Validator\ErrorElement;

class TagPool extends Pool
{

    /**
     * @param string                 $name
     * @param TagProviderInterface $instance
     *
     * @return void
     */
    public function addProvider($name, TagProviderInterface $instance)
    {
        $this->providers[$name] = $instance;
    }

    /**
     * @param \Sonata\AdminBundle\Validator\ErrorElement $errorElement
     * @param \Sonata\ClassificationBundle\Model\TagInterface   $tag
     *
     * @return void
     */
    public function validate(ErrorElement $errorElement, TagInterface $tag)
    {
    }
}
