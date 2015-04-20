<?php

namespace Rz\ClassificationBundle\Admin;

use Sonata\ClassificationBundle\Admin\CategoryAdmin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Rz\ClassificationBundle\Provider\CategoryPool;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryAdmin extends BaseAdmin
{
    const CATEGORY_DEFAULT_CONTEXT = 'default';
    protected $contextManager;
    protected $categoryManager;
    protected $pageManager;
    protected $siteManager;
    protected $mediaManager;
    protected $pool;
    protected $slugGenerator;
    protected $controllerEnabled = true;
    protected $defaultPageTemplate;


    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('parent.name', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone'))))
            ->add('context.name', null,  array('footable'=>array('attr'=>array('data_hide'=>'phone, tablet'))))
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
        $category = $this->getSubject();

        if (!$this->controllerEnabled && interface_exists('Sonata\PageBundle\Model\PageInterface')) {
            $formMapper
                ->with('Category', array('class' => 'col-md-6'))
                ->add('hasPage', null, array('required' => false))
                ->end();

            if ($this->getSubject()->getHasPage()) {
                $formMapper
                    ->with('Category', array('class' => 'col-md-6'))
                    ->add('page', 'sonata_type_model_list', array('btn_list' => false, 'btn_add' => false, 'btn_delete' => false))
                    ->end();
            }
        }

        $formMapper
            ->with('Category', array('class' => 'col-md-6'))
                ->add('name')
                ->add('description', 'textarea', array('required' => false))
                ->add('enabled')
                ->add('position', 'hidden', array('required' => false, 'data' => 0))
            ;

            if($this->getSubject()->getParent() !== null || $this->getSubject()->getId() === null) {
                $formMapper->add('parent', 'sonata_category_selector', array(
                    'category'      => $this->getSubject() ?: null,
                    'model_manager' => $this->getModelManager(),
                    'class'         => $this->getClass(),
                    'required'      => false,
                    'context'       => $this->getSubject()->getContext()
                ));
            }
            $formMapper
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
                ->with('Category')
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

        if($provider = $this->getPoolProvider()) {
            if ($category->getId()) {
                $provider->load($category);
                $provider->buildEditForm($formMapper, $category);
            } else {
                $provider->buildCreateForm($formMapper, $category);
            }
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
                //->add('parent')
            ->end()
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $showMapper
                ->with('General')
                    ->add('media')
                ->end();
        }
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

    /**
     * @return mixed
     */
    public function getCategoryManager()
    {
        return $this->categoryManager;
    }

    /**
     * @param mixed $categoryManager
     */
    public function setCategoryManager(CategoryManagerInterface $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function setPool(CategoryPool $pool) {
        $this->pool = $pool;
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

    protected function fetchCurrentContext() {

        $context_param = $this->getPersistentParameter('context');
        $context = null;
        if($context_param) {
            $context = $this->contextManager->find($context_param);
        } else {
            $context = $this->contextManager->findOneBy(array('id'=>self::CATEGORY_DEFAULT_CONTEXT));
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

        if ($this->getPoolProvider()) {
            $this->getPoolProvider()->postUpdate($object);
        }

        if (!$this->controllerEnabled && interface_exists('Sonata\PageBundle\Model\PageInterface')) {
            // create page for collection list default collection is placed on localhost.
            // for multi site is has to be moved via the Page module.
            $site = $this->siteManager->findOneBy(array('name' => 'localhost'));
            $parent = null;
            //has parent
            if($tier1 = $object->getParent()) {
                if ($tier1->getPage()) {
                    $parent = $tier1->getPage();
                }
            }

            if (!$parent) {
                $parent = $this->pageManager->findOneBy(array('name' => 'homepage'));
            }

            if ($object->getHasPage() && !$object->getPage()) {
                if ($site) {
                    $page = $this->pageManager->create();
                    $this->setPageDetails($page, $object, $parent);
                    $page->setParent($parent);
                    $page->setSite($site);
                    $this->pageManager->save($page);
                }
                $object->setPage($page);
                // delete reference if hasPage is set to false
            } elseif (!$object->getHasPage() && $object->getPage()) {
                $object->setPage(null);
//                $result = $this->getModelManager()->update($object);
//
//                if(null != $result) {
//                    $object = $result;
//                }
//
//                $page = $this->pageManager->findOneBy(array('slug' => $object->getSlug()));
//                if ($page) {
//                    $this->pageManager->delete($page);
//                }

            } elseif ($page = $object->getPage()) {
                $this->setPageDetails($page, $object, $parent);
                $page->setParent($parent);
                $object->setPage($page);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        parent::postPersist($object);

        if ($this->getPoolProvider()) {
            $this->getPoolProvider()->postPersist($object);
        }

        if (!$this->controllerEnabled && interface_exists('Sonata\PageBundle\Model\PageInterface')) {
            // create page for collection list default collection is placed on localhost.
            // for multi site is has to be moved via the Page module.

            $site = $this->siteManager->findOneBy(array('name' => 'localhost'));
            $parent = null;

            //has parent
            if($tier1 = $object->getParent()) {
                if ($tier1->getPage()) {
                    $parent = $tier1->getPage();
                }
            }

            if (!$parent) {
                $parent = $this->pageManager->findOneBy(array('name' => 'homepage'));
            }


            if ($object->getHasPage() && !$object->getPage()) {
                if ($site) {
                    $page = $this->pageManager->create();
                    $this->setPageDetails($page, $object, $parent);
                    $page->setParent($parent);
                    $page->setSite($site);
                    $this->pageManager->save($page);
                }

                $object->setPage($page);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($object)
    {
        if($object->getParent()) {
            $children = $object->getChildren();
            if(count($children) > 0) {
                foreach($children as $child) {
                    $child->setParent(null);
                    $object->removeChild($child);
                }
                $object->setParent(null);
            } else {
                $object->setParent(null);
            }
        }
    }

    protected function setPageDetails($page, $object, $parent) {

        $slug = $object->getSlug();
        //verify is URL exist
        $similarPage = $this->pageManager->findDuplicateUrl($parent, $page, $slug);

        $count = count($similarPage);
        if ($count > 1) {
            $slug = sprintf('%s-%s', $slug, ++$count);
        }
        $url = sprintf('/%s', $slug);

        $page->setSlug($slug);
        $page->setUrl($url);
        $page->setName($object->getName());
        $page->setPageAlias($this->slugGenerator->generateSlug($slug, '_'));
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        //TODO set default template on configuration
        $page->setTemplateCode('rzcms_blog');
        $page->setRouteName('page_slug');

        $settings = $object->getSettings();
        if ($settings) {

            if(isset($settings['seoTitle'])) {
                $page->setTitle($settings['seoTitle']);
            }

            if(isset($settings['seoMetaKeyword'])) {
                $page->setMetaKeyword($settings['seoMetaKeyword']);
            }

            if(isset($settings['seoMetaDescription'])) {
                $page->setMetaDescription($settings['seoMetaDescription']);
            }

            $page->setOgTitle(isset($settings['ogTitle']) ? $settings['ogTitle'] : null);
            $page->setOgType(isset($settings['ogType']) ? $settings['ogType'] : null);
            $page->setOgDescription(isset($settings['ogDescription']) ? $settings['ogDescription'] : null);
            if(isset($settings['ogImage'])) {
                $media = $this->mediaManager->findOneBy(array('id'=>$settings['ogImage']));
                $page->setOgImage($media ?: null);
            }
        }
    }

}
