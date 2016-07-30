<?php

namespace Rz\ClassificationBundle\Provider\Tag;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\TagInterface;
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
            ->with('tab.group.rz_classification_tag_settings')
                ->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys($formMapper, $object), 'required'=>false, 'label'=>false, 'attr'=>array('class'=>'rz-immutable-container')))
            ->end();
    }


    /**
     * {@inheritdoc}
     */
    public function prePersist(TagInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function preUpdate(TagInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function postPersist(TagInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function postUpdate(TagInterface $object){}

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, TagInterface $object){}

    public function load(TagInterface $object) {}

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
