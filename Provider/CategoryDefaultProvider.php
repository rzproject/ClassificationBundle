<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\CategoryInterface;

class CategoryDefaultProvider extends BaseCategoryProvider
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
            ->with('Settings', array('class' => 'col-md-6'))
                ->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys($formMapper, $object)))
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     * @param null $object
     * @return array
     */
    public function getFormSettingsKeys(FormMapper $formMapper, $object = null)
    {
        $settings = array(
            array('seoTitle', 'text', array('required' => false, 'attr'=>array('class'=>'span8'))),
            array('seoMetaKeyword', 'textarea', array('required' => false, 'attr'=>array('class'=>'span8', 'rows'=>5))),
            array('seoMetaDescription', 'textarea', array('required' => false, 'attr'=>array('class'=>'span8', 'rows'=>5))),
            array('ogTitle', 'text', array('required' => false, 'attr'=>array('class'=>'span8'))),
            array('ogType', 'choice', array('choices'=>$this->getMetatagChoices(), 'attr'=>array('class'=>'span4'))),
            array('ogDescription', 'textarea', array('required' => false, 'attr'=>array('class'=>'span8', 'rows'=>5))),
        );

        if($this->controllerEnabled) {
            $settings = array_merge(array(array('template', 'choice', array('choices'=>$this->getTemplateChoices($object)))), $settings);
            $settings = array_merge(array(array('ajaxTemplate', 'choice', array('choices'=>$this->getAjaxTemplateChoices($object)))), $settings);
            $settings = array_merge(array(array('ajaxPagerTemplate', 'choice', array('choices'=>$this->getAjaxPagerTemplateChoices($object)))), $settings);
        }

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            array_push($settings, array($this->getMediaBuilder($formMapper), null, array()));
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(CategoryInterface $category)
    {
        $category->setSetting('ogImage', is_object($category->getSetting('ogImage')) ? $category->getSetting('ogImage')->getId() : null);
        parent::prePersist($category);
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(CategoryInterface $category)
    {
        $category->setSetting('ogImage', is_object($category->getSetting('ogImage')) ? $category->getSetting('ogImage')->getId() : null);
        parent::prePersist($category);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, CategoryInterface $category)
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

    public function load(CategoryInterface $category) {
        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            //load media
            $media = $category->getSetting('ogImage', null);
            if (is_int($media)) {
                $media = $this->mediaManager->findOneBy(array('id' => $media));
            }
            $category->setSetting('ogImage', $media);
        }
    }

    protected function getMediaBuilder(FormMapper $formMapper) {

        // simulate an association media...
        $fieldDescription = $this->mediaAdmin->getModelManager()->getNewFieldDescriptionInstance($this->mediaAdmin->getClass(), 'media');
        $fieldDescription->setAssociationAdmin($this->mediaAdmin);
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setOption('edit', 'list');
        $fieldDescription->setOptions(array('link_parameters' => array('context' => 'sonata_category', 'hide_context' => true)));
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

    protected function getCategoryChoices($templates, $filter  = 'post') {
        $list = array();

        foreach($templates as $key=>$value) {
            if ($value['type'] == $filter) {
                $list[$value['path']] = $value['name'].' - '.$value['path'];
            }
        }
        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateChoices($object = null)
    {
        if($object->getParent() != null && $object->getParent()->getSlug() == 'news') {
            return $this->getCategoryChoices($this->templates, 'category');
        } else {
            return $this->getCategoryChoices($this->templates, 'post');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAjaxTemplateChoices($object = null)
    {
        if($object->getParent() != null && $object->getParent()->getSlug() == 'news') {
            return $this->getCategoryChoices($this->ajaxTemplates, 'category');
        } else {
            return $this->getCategoryChoices($this->ajaxTemplates, 'post');
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getAjaxPagerTemplateChoices($object = null)
    {
        if($object->getParent() != null && $object->getParent()->getSlug() == 'news') {
            return $this->getCategoryChoices($this->ajaxPagerTemplates, 'category');
        } else {
            return $this->getCategoryChoices($this->ajaxPagerTemplates, 'post');
        }
    }
}
