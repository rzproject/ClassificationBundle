<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\TagInterface;

abstract class BaseTagProvider extends BaseProvider  implements TagProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function preRemove(TagInterface $category)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(TagInterface $category)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(TagInterface $category)
    {
        $category->setCreatedAt(new \Datetime());
        $category->setUpdatedAt(new \Datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(TagInterface $category)
    {
        $category->setUpdatedAt(new \Datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, TagInterface $category)
    {

    }
}
