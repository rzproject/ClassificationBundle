<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\TagInterface;

class TagDefaultProvider extends BaseTagProvider
{

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        $this->buildCreateForm($formMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys()));
    }

    /**
     * @return array
     */
    public function getFormSettingsKeys()
    {
        return array(
            array('template', 'choice', array('choices'=>$this->getTemplateChoices())),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(TagInterface $tag)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(TagInterface $tag)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, TagInterface $tag)
    {
    }
}
