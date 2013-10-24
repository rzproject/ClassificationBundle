<?php

namespace Rz\ClassificationBundle\Admin;

use Sonata\ClassificationBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends BaseAdmin
{
    protected $formOptions = array(
        'cascade_validation' => true
    );

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('name')
            ->add('description', 'textarea', array('required' => false))
            ->add('position', 'integer', array('required' => false, 'data' => 0))
            ->add('parent', 'sonata_category_selector', array(
                'category'      => $this->getSubject() ?: null,
                'model_manager' => $this->getModelManager(),
                'class'         => $this->getClass(),
                'required'      => false
            ))
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $formMapper->add('media', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'sonata_category',
            ));
        }
    }
}
