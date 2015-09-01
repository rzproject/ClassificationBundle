<?php

namespace Rz\ClassificationBundle\Admin;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\ClassificationBundle\Admin\CollectionAdmin as BaseClass;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Rz\ClassificationBundle\Provider\CollectionPool;

class CollectionAdmin extends BaseClass
{
    const COLLECTION_DEFAULT_CONTEXT = 'default';
    protected $contextManager;
    protected $pageManager;
    protected $siteManager;
    protected $mediaManager;
    protected $pool;
    protected $slugGenerator;
    protected $controllerEnabled = true;

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name',null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone'))))
            ->add('createdAt', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('updatedAt', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
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
        $collection = $this->getSubject();

        $formMapper
            ->with('Collection', array('class' => 'col-md-6'))
                ->add('enabled', null, array('required' => false))
                //->add('context', 'sonata_type_model_list', array('required' => false,))
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
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {

            $formMapper
                ->with('Collection', array('class' => 'col-md-6'))
                    ->add('media', 'sonata_type_model_list',
                    array('required' => false),
                    array(
                        'link_parameters' => array(
                            'provider' => 'sonata.media.provider.image',
                            'context'  => 'sonata_collection'
                        )
                    ))
                ->end()
            ;
        }

        if($provider = $this->getPoolProvider()) {
            if ($collection->getId()) {
                $provider->load($collection);
                $provider->buildEditForm($formMapper);
            } else {
                $provider->buildCreateForm($formMapper);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description')
            ->add('enabled')
            ->add('slug')
            ->add('contentFormatter')
            ->add('content')
            ->add('createdAt')
            ->add('updatedAt')
        ;
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

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('context')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $defaultContext = $this->contextManager->find('default');

        if (!$defaultContext) {
            throw new NotFoundHttpException('Default context should be defined');
        }

        $parameters = array(
            'context'      => $defaultContext->getId(),
            'hide_context' => (int)$this->getRequest()->get('hide_context', 0)
        );


        if ($this->getSubject()) {
            $parameters['context'] = $this->getSubject()->getContext() ? $this->getSubject()->getContext()->getId() : $defaultContext->getId();
            return $parameters;
        }

        if ($this->hasRequest()) {
            $parameters['context'] = $this->getRequest()->get('context') ?: $defaultContext->getId();

            return $parameters;
        }

        return $parameters;
    }

    public function setContextManager(ContextManagerInterface $contextManager) {
        $this->contextManager = $contextManager;
    }

    /**
     * @return mixed
     */
    public function getPageManager()
    {
        return $this->pageManager;
    }

    /**
     * @param mixed $pageManager
     */
    public function setPageManager($pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * @return mixed
     */
    public function getSiteManager()
    {
        return $this->siteManager;
    }

    /**
     * @param mixed $siteManager
     */
    public function setSiteManager($siteManager)
    {
        $this->siteManager = $siteManager;
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

    /**
     * @return mixed
     */
    public function getSlugGenerator()
    {
        return $this->slugGenerator;
    }

    /**
     * @param mixed $slugGenerator
     */
    public function setSlugGenerator($slugGenerator)
    {
        $this->slugGenerator = $slugGenerator;
    }

    /**
     * @return boolean
     */
    public function isControllerEnabled()
    {
        return $this->controllerEnabled;
    }

    /**
     * @param boolean $controllerEnabled
     */
    public function setControllerEnabled($controllerEnabled)
    {
        $this->controllerEnabled = $controllerEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($collection)
    {
        $parameters = $this->getPersistentParameters();
        $parameters['context'] = $parameters['context']?:'default';
        $context = $this->contextManager->find($parameters['context']);
        $collection->setContext($context);
    }

    public function setPool(CollectionPool $pool) {
        $this->pool = $pool;
    }

    protected function fetchCurrentContext() {

        $context_param = $this->getPersistentParameter('context');
        $context = null;
        if($context_param) {
            $context = $this->contextManager->find($context_param);
        } else {
            $context = $this->contextManager->findOneBy(array('id'=>self::COLLECTION_DEFAULT_CONTEXT));
        }

        if($context) {
            return $context;
        } else {
            return;
        }
    }

    protected function getPoolProvider() {

        $currentContext = $this->fetchCurrentContext();

        $context = str_replace('-', '_', $currentContext->getId());

        if ($this->pool->hasContext($context)) {
            $providerName = $this->pool->getProviderNameByContext($context);
            return $this->pool->getProvider($providerName);
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        parent::postUpdate($object);
        //$this->getPoolProvider()->postUpdate($object);
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        parent::postPersist($object);
        //$this->getPoolProvider()->postPersist($object);
    }
}
