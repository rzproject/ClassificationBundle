<?php

namespace Rz\ClassificationBundle\Admin;

use Sonata\ClassificationBundle\Admin\TagAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TagAdmin extends Admin
{
    protected $pool;
    protected $defaultContext;
    protected $contextManager;

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $provider = $this->getPoolProvider($this->pool);

        if($provider) {
            $tabSettings = array('class' => 'col-md-4');
        } else {
            $tabSettings = array('class' => 'col-md-8');
        }

        $formMapper->with('tab.group.rz_classification_tag_general', $tabSettings)->end();

        if($provider) {
            $formMapper->with('tab.group.rz_classification_tag_settings', array('class' => 'col-md-8'))->end();
        }


        $formMapper
            ->with('tab.group.rz_classification_tag_general')
                ->add('name')
            ->end()
        ;

        if ($this->hasSubject() && $this->getSubject()->getId()) {
            $formMapper
                ->with('tab.group.rz_classification_tag_general')
                    ->add('slug')
                ->end();
        }

        $formMapper
            ->with('tab.group.rz_classification_tag_general')
                ->add('enabled', null, array('required' => false))
            ->end();

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
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('context', null, array('show_filter' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('slug', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm', 'md')))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs')))))
            ->add('createdAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('updatedAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
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

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if ($contextId = $this->getPersistentParameter('context')) {

            $context = $this->contextManager->find($contextId);
            if (!$context) {
                $context = $this->contextManager->create();
                $context->setEnabled(true);
                $context->setId($context);
                $context->setName($context);

                $this->contextManager->save($context);
            }

            $instance->setContext($context);
        }

        return $instance;
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
     * @param mixed $defaultContext
     */
    public function setDefaultContext($defaultContext)
    {
        $this->defaultContext = $defaultContext;
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
}
