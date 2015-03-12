<?php
namespace Rz\ClassificationBundle\Provider;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

interface CategoryProviderInterface extends ClassificationProviderInterface
{
    /**
     *
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function preUpdate(CategoryInterface $category);

    /**
     *
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function postUpdate(CategoryInterface $category);

    /**
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function preRemove(CategoryInterface $category);

    /**
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function postRemove(CategoryInterface $category);


    /**
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function prePersist(CategoryInterface $category);

    /**
     *
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function postPersist(CategoryInterface $category);

    /**
     * @param ErrorElement   $errorElement
     * @param CategoryInterface $category
     */
    public function validate(ErrorElement $errorElement, CategoryInterface $category);

}
