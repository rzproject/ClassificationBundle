<?php

namespace Rz\ClassificationBundle\Provider\Category;

use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
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
    public function buildEditForm(FormMapper $formMapper, $object = null)
    {
        $this->buildCreateForm($formMapper, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper, $object = null)
    {
        $formMapper
            ->tab('tab.rz_classification_category_settings')
                ->with('tab.group.rz_classification_category_settings', array('class' => 'col-md-8'))
                    ->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys($formMapper, $object), 'required'=>false, 'label'=>false, 'attr'=>array('class'=>'rz-immutable-container')))
                ->end()
            ->end();
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
