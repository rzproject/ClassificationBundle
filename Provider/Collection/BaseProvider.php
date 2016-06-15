<?php

namespace Rz\ClassificationBundle\Provider\Collection;

use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\CollectionInterface;
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
    public function prePersist(CollectionInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function preUpdate(CollectionInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function postPersist(CollectionInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function postUpdate(CollectionInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, CollectionInterface $object){}

    public function load(CollectionInterface $object) {}

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
