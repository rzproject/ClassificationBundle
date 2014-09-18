<?php

namespace Rz\ClassificationBundle\Admin;

use Sonata\ClassificationBundle\Admin\CategoryAdmin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CategoryAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('description', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone'))))
            ->add('parent', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'Show' => array('template' => 'SonataAdminBundle:CRUD:list__action_show.html.twig'),
                    'Edit' => array('template' => 'SonataAdminBundle:CRUD:list__action_edit.html.twig'),
                    'Delete' => array('template' => 'SonataAdminBundle:CRUD:list__action_delete.html.twig')),
                'footable'=>array('attr'=>array('data_hide'=>'phone,tablet')),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))
                ->add('name')
                ->add('description', 'textarea', array('required' => false))
                ->add('content', 'sonata_formatter_type', array(
                    'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
                    'format_field'   => 'contentFormatter',
                    'source_field'   => 'rawContent',
                    'ckeditor_context' => 'news',
                    'source_field_options'      => array(
                        'attr' => array('class' => 'span12', 'rows' => 20)
                    ),
                    'target_field'   => 'content',
                    'listener'       => true,
                ))
            ->end()
            ->with('Options', array('class' => 'col-md-6'))
                ->add('position', 'integer', array('required' => false, 'data' => 0))
                ->add('parent', 'sonata_category_selector', array(
                    'category'      => $this->getSubject() ?: null,
                    'model_manager' => $this->getModelManager(),
                    'class'         => $this->getClass(),
                    'select2'       => true,
                    'required'      => false
                ))
            ->end()
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $formMapper
                ->with('General')
                    ->add('media', 'sonata_type_model_list',
                        array('required' => false),
                        array(
                            'link_parameters' => array(
                                'provider' => 'sonata.media.provider.image',
                                'context'  => 'sonata_category',
                            )
                        )
                    )
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General', array('class' => 'col-md-6'))
                ->add('name')
                ->add('description')
                ->add('slug')
                ->add('contentFormatter')
                ->add('content')
                ->add('createdAt')
                ->add('updatedAt')
            ->end()
            ->with('Options', array('class' => 'col-md-6'))
                ->add('position')
                ->add('parent')
            ->end()
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $showMapper
                ->with('General')
                    ->add('media')
                ->end();
        }
    }
}
