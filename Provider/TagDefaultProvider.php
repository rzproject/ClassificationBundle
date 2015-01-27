<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\TagInterface;

class TagDefaultProvider extends BaseTagProvider
{

    protected $mediaAdmin;
    protected $mediaManager;

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
        $formMapper
            ->with('Settings', array('class' => 'col-md-6'))
                ->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys($formMapper)))
            ->end();
    }

    /**
     * @return array
     */
    public function getFormSettingsKeys(FormMapper $formMapper)
    {
        $settings = array(
            array('template', 'choice', array('choices'=>$this->getTemplateChoices())),
            array('seoTitle', 'text', array('required' => false, 'attr'=>array('class'=>'span8'))),
            array('seoMetaKeyword', 'textarea', array('required' => false, 'attr'=>array('class'=>'span8', 'rows'=>5))),
            array('seoMetaDescription', 'textarea', array('required' => false, 'attr'=>array('class'=>'span8', 'rows'=>5))),
            array('ogTitle', 'text', array('required' => false, 'attr'=>array('class'=>'span8'))),
            array('ogType', 'choice', array('choices'=>$this->getMetatagChoices(), 'attr'=>array('class'=>'span4'))),
            array('ogDescription', 'textarea', array('required' => false, 'attr'=>array('class'=>'span8', 'rows'=>5))),
        );

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            array_push($settings, array($this->getMediaBuilder($formMapper), null, array()));
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(TagInterface $tag)
    {
        $tag->setSetting('ogImage', is_object($tag->getSetting('ogImage')) ? $tag->getSetting('ogImage')->getId() : null);
        parent::prePersist($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(TagInterface $tag)
    {
        $tag->setSetting('ogImage', is_object($tag->getSetting('ogImage')) ? $tag->getSetting('ogImage')->getId() : null);
        parent::prePersist($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, TagInterface $tag)
    {
    }

    /**
     * @return mixed
     */
    public function getMediaAdmin()
    {
        return $this->mediaAdmin;
    }

    /**
     * @param mixed $mediaAdmin
     */
    public function setMediaAdmin($mediaAdmin)
    {
        $this->mediaAdmin = $mediaAdmin;
    }

    /**
     * @return mixed
     */
    public function getMediaManager()
    {
        return $this->mediaManager;
    }

    /**
     * @param mixed $mediaManager
     */
    public function setMediaManager($mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function load(TagInterface $tag) {

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            //load media
            $media = $tag->getSetting('ogImage', null);
            if (is_int($media)) {
                $media = $this->mediaManager->findOneBy(array('id' => $media));
            }
            $tag->setSetting('ogImage', $media);
        }
    }

    protected function getMediaBuilder(FormMapper $formMapper) {

        // simulate an association media...
        $fieldDescription = $this->mediaAdmin->getModelManager()->getNewFieldDescriptionInstance($this->mediaAdmin->getClass(), 'media');
        $fieldDescription->setAssociationAdmin($this->mediaAdmin);
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setOption('edit', 'list');
        $fieldDescription->setAssociationMapping(array(
            'fieldName' => 'media',
            'type'      => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE
        ));

        return $formMapper->create('ogImage', 'sonata_type_model_list', array(
            'sonata_field_description' => $fieldDescription,
            'class'                    => $this->mediaAdmin->getClass(),
            'model_manager'            => $this->mediaAdmin->getModelManager()),
            array('link_parameters' => array('context' => 'news', 'hide_context' => true))
        );
    }
}
