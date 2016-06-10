<?php

namespace Rz\ClassificationBundle\Provider\Category;

use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Rz\ClassificationBundle\Provider\BaseProvider as Provider;

abstract class BaseProvider extends Provider
{
    protected $slugify;

    /**
     * @param string                                           $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * @param mixed $rawSettings
     */
    public function setRawSettings($rawSettings)
    {
        parent::setRawSettings($rawSettings);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(CategoryInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function preUpdate(CategoryInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function postPersist(CategoryInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function postUpdate(CategoryInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, CategoryInterface $object){}

    public function load(CategoryInterface $object) {}

    /**
     * @return mixed
     */
    public function getSlugify()
    {
        return $this->slugify;
    }

    /**
     * @param mixed $slugify
     */
    public function setSlugify($slugify)
    {
        $this->slugify = $slugify;
    }
}
