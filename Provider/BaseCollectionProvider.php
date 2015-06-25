<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\CollectionInterface;

abstract class BaseCollectionProvider extends BaseProvider implements CollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function preRemove(CollectionInterface $collection)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(CollectionInterface $collection)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(CollectionInterface $collection)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(CollectionInterface $collection)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, CollectionInterface $collection)
    {

    }
}
