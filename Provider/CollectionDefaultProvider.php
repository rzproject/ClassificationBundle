<?php

namespace Rz\ClassificationBundle\Provider;


use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\CollectionInterface;

class CollectionDefaultProvider extends BaseCollectionProvider
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
            array('type', 'choice', array('choices'=>$this->getCollectionTypeChoices())),
        );

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            array_push($settings, array($this->getMediaBuilder($formMapper), null, array()));
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(CollectionInterface $collection)
    {
        $collection->setSetting('image', is_object($collection->getSetting('image')) ? $collection->getSetting('image')->getId() : null);
        parent::prePersist($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(CollectionInterface $collection)
    {
        $collection->setSetting('ogImage', is_object($collection->getSetting('ogImage')) ? $collection->getSetting('ogImage')->getId() : null);
        parent::prePersist($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, CollectionInterface $collection)
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

    public function load(CollectionInterface $collection) {

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            //load media
            $media = $collection->getSetting('image', null);
            if (is_int($media)) {
                $media = $this->mediaManager->findOneBy(array('id' => $media));
            }
            $collection->setSetting('image', $media);
        }
    }

    protected function getMediaBuilder(FormMapper $formMapper) {

        // simulate an association media...
        $fieldDescription = $this->mediaAdmin->getModelManager()->getNewFieldDescriptionInstance($this->mediaAdmin->getClass(), 'media');
        $fieldDescription->setAssociationAdmin($this->mediaAdmin);
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setOption('edit', 'list');
        $fieldDescription->setOptions(array('link_parameters' => array('context' => 'sonata_collection', 'hide_context' => true)));
        $fieldDescription->setAssociationMapping(array(
            'fieldName' => 'media',
            'type'      => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE
        ));

        return $formMapper->create('image', 'sonata_type_model_list', array(
            'sonata_field_description' => $fieldDescription,
            'class'                    => $this->mediaAdmin->getClass(),
            'model_manager'            => $this->mediaAdmin->getModelManager())
        );
    }

    protected function getCollectionTypeChoices() {
        return array('system'=>'System', 'custom'=>'Custom');
    }
}
