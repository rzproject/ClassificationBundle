<?php
namespace Rz\ClassificationBundle\Provider;

use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

interface TagProviderInterface extends ClassificationProviderInterface
{
    /**
     *
     * @param TagInterface $tag
     *
     * @return void
     */
    public function preUpdate(TagInterface $tag);

    /**
     *
     * @param TagInterface $tag
     *
     * @return void
     */
    public function postUpdate(TagInterface $tag);

    /**
     * @param TagInterface $tag
     *
     * @return void
     */
    public function preRemove(TagInterface $tag);

    /**
     * @param TagInterface $tag
     *
     * @return void
     */
    public function postRemove(TagInterface $tag);


    /**
     * @param TagInterface $tag
     *
     * @return void
     */
    public function prePersist(TagInterface $tag);

    /**
     *
     * @param TagInterface $tag
     *
     * @return void
     */
    public function postPersist(TagInterface $tag);

    /**
     * @param ErrorElement   $errorElement
     * @param TagInterface $tag
     */
    public function validate(ErrorElement $errorElement, TagInterface $tag);

}
