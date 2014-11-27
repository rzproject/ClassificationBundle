<?php

namespace Rz\ClassificationBundle\Provider;


use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\CategoryInterface;

abstract class BaseCategoryProvider extends BaseProvider implements CategoryProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function preRemove(CategoryInterface $category)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(CategoryInterface $category)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(CategoryInterface $category)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(CategoryInterface $category)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, CategoryInterface $category)
    {

    }
}
