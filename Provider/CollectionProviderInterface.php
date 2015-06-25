<?php
namespace Rz\ClassificationBundle\Provider;

use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

interface CollectionProviderInterface extends ClassificationProviderInterface
{
    /**
     *
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function preUpdate(CollectionInterface $collection);

    /**
     *
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function postUpdate(CollectionInterface $collection);

    /**
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function preRemove(CollectionInterface $collection);

    /**
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function postRemove(CollectionInterface $collection);


    /**
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function prePersist(CollectionInterface $collection);

    /**
     *
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function postPersist(CollectionInterface $collection);

    /**
     * @param ErrorElement   $errorElement
     * @param CollectionInterface $collection
     */
    public function validate(ErrorElement $errorElement, CollectionInterface $collection);

}
