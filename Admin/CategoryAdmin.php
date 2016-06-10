<?php

namespace Rz\ClassificationBundle\Admin;

use Sonata\ClassificationBundle\Admin\CategoryAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\CoreBundle\Validator\ErrorElement;


class CategoryAdmin extends Admin
{
    protected $pool;
    protected $defaultContext;
    protected $contextManager;


    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $routes)
    {
        $routes->add('tree', 'tree');
        $routes->add('createBaseCategory', 'createBaseCategory');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $provider = $this->getPoolProvider();

        $formMapper
            ->tab('tab.rz_classification_category_general')
                ->with('tab.group.rz_classification_category_general', array('class' => 'col-md-8'))->end()
                ->with('tab.group.rz_classification_category_options', array('class' => 'col-md-4'))->end()
            ->end();

        if($provider) {
            $formMapper
                ->tab('tab.rz_classification_category_settings')
                    ->with('tab.group.rz_classification_category_settings', array('class' => 'col-md-8'))->end()
                ->end();
        }

        $formMapper
            ->tab('tab.rz_classification_category_general')
                ->with('tab.group.rz_classification_category_general')
                    ->add('name')
                    ->add('description', 'textarea', array('required' => false))
                ->end()
            ->end();

        if ($this->hasSubject()) {
            if ($this->getSubject()->getParent() !== null || $this->getSubject()->getId() === null) { // root category cannot have a parent

                $formMapper
                    ->tab('tab.rz_classification_category_general')
                        ->with('tab.group.rz_classification_category_general')
                          ->add('parent', 'sonata_category_selector', array(
                              'category'      => $this->getSubject() ?: null,
                              'model_manager' => $this->getModelManager(),
                              'class'         => $this->getClass(),
                              'required'      => true,
                              'context'       => $this->getSubject()->getContext() ?: $this->getDefaultContext(),
                            ))
                        ->end()
                    ->end();
            }
        }

        $position = $this->hasSubject() && !is_null($this->getSubject()->getPosition()) ? $this->getSubject()->getPosition() : 0;

        $formMapper
            ->tab('tab.rz_classification_category_general')
                ->with('tab.group.rz_classification_category_options')
                    ->add('enabled', null, array(
                        'required' => false,
                    ))
                    ->add('position', 'integer', array(
                        'required' => false,
                        'data'     => $position,
                    ))
                ->end()
            ->end()
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $formMapper
                ->tab('tab.rz_classification_category_general')
                    ->with('tab.group.rz_classification_category_general')
                        ->add('media', 'sonata_type_model_list',
                            array(
                                'required' => false,
                            ),
                            array(
                                'link_parameters' => array(
                                    'provider' => 'sonata.media.provider.image',
                                    'context'  => 'sonata_category',
                                ),
                            )
                        )
                    ->end()
                ->end();
        }

        if($provider) {
            $instance = $this->getSubject();
            if ($instance && $instance->getId()) {
                $provider->load($instance);
                $provider->buildEditForm($formMapper);
            } else {
                $provider->buildCreateForm($formMapper);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $options = [];
        if ($this->getPersistentParameter('hide_context') === 1) {
            $options['disabled'] = true;
        }

        $filterParam = [];
        if ($this->hasRequest() && !$this->getRequest()->get('filter') && !$this->getRequest()->get('filters')) {
            $filterParam = array('show_filter'=>false);
        }

        $datagridMapper
            ->add('name')
            ->add('context', null, $filterParam, null, $options)
            ->add('enabled')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('context', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('slug', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('description', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs')))))
            ->add('parent', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm', 'md')))))
        ;
    }

    /**
     * @return mixed
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * @param mixed $pool
     */
    public function setPool($pool)
    {
        $this->pool = $pool;
    }

    /**
     * @return mixed
     */
    public function getDefaultContext()
    {
        return $this->defaultContext;
    }

    /**
     * @return mixed
     */
    public function getContextManager()
    {
        return $this->contextManager;
    }

    /**
     * @param mixed $contextManager
     */
    public function setContextManager($contextManager)
    {
        $this->contextManager = $contextManager;
    }

    /**
     * @param mixed $defaultContext
     */
    public function setDefaultContext($defaultContext)
    {
        $this->defaultContext = $defaultContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $parameters = array(
            'context'      => $this->getDefaultContext(),
            'hide_context' => $this->hasRequest() ? (int) $this->getRequest()->get('hide_context', 0) : 0,
        );

        if ($this->getSubject()) {
            $parameters['context'] = $this->getSubject()->getContext() ? $this->getSubject()->getContext()->getId() : $this->getDefaultContext();

            return $parameters;
        }

        if ($this->hasRequest()) {
            $parameters['context'] = $this->getRequest()->get('context');

            return $parameters;
        }

        return $parameters;
    }

    protected function fetchCurrentContext() {

        $contextCode = $this->getPersistentParameter('context');

        $context = null;
        if($contextCode) {
            $context = $this->contextManager->find($contextCode);
        } else {
            $context = $this->contextManager->find($this->getDefaultContext());
        }

        if($context) {
            return $context;
        } else {
            return;
        }
    }

    protected function getPoolProvider() {
        $currentContext = $this->fetchCurrentContext();

        if ($this->pool->hasContext($currentContext->getId())) {
            $providerName = $this->pool->getProviderNameByContext($currentContext->getId());
            return $this->pool->getProvider($providerName);
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        parent::prePersist($object);
        if($provider = $this->getPoolProvider()) {
            $provider->prePersist($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        parent::preUpdate($object);
        if($provider = $this->getPoolProvider()) {
            $provider->preUpdate($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        parent::postUpdate($object);
        if($provider = $this->getPoolProvider()) {
            $provider->postUpdate($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        parent::postPersist($object);
        if($provider = $this->getPoolProvider()) {
            $provider->postPersist($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        parent::validate($errorElement, $object);

        if($provider = $this->getPoolProvider()) {
            $provider->validate($errorElement, $object);
        }
    }
}
