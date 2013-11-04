<?php

namespace Rz\ClassificationBundle\Admin;

use Sonata\ClassificationBundle\Admin\CollectionAdmin as BaseClass;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CollectionAdmin extends BaseClass
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name',null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('slug', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('description', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone'))))
        ;
    }
}
