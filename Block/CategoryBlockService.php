<?php

namespace Rz\ClassificationBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;

class CategoryBlockService extends BaseBlockService
{
    protected $categoryManager;
    protected $categoryAdmin;
    protected $templates;
    protected $ajaxTemplates;
    protected $maxPerPage;

    /**
     * @param string          $name
     * @param EngineInterface $templating
     */
    public function __construct($name,
                                EngineInterface $templating,
                                ManagerInterface $categoryManager,
                                AdminInterface $categoryAdmin,
                                $templates,
                                $ajaxTemplates,
                                $maxPerPage)
    {
        $this->name       = $name;
        $this->templating = $templating;
        $this->categoryManager = $categoryManager;
        $this->categoryAdmin = $categoryAdmin;
        $this->templates = $templates;
        $this->ajaxTemplates = $ajaxTemplates;
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block) {

        if (!$block->getSetting('category') instanceof CategoryInterface) {
            $this->load($block);
        }

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array($this->getCategoryBuilder($formMapper), null, array('attr'=>array('class'=>'span8'))),
                array('mode', 'choice', array(
                    'choices' => array(
                        'public' => 'public',
                        'admin'  => 'admin'
                    )
                )),
                array('template', 'choice', array('choices' => $this->templates)),
                array('ajaxTemplate', 'choice', array('choices' => $this->ajaxTemplates)),
            )
        ));
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return \Symfony\Component\Form\FormBuilder
     */
    protected function getCategoryBuilder(FormMapper $formMapper)
    {
        // simulate an association ...
        $fieldDescription = $this->categoryAdmin->getModelManager()->getNewFieldDescriptionInstance($this->categoryAdmin->getClass(), 'category' );
        $fieldDescription->setAssociationAdmin($this->categoryAdmin);
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setOption('edit', 'list');
        $fieldDescription->setAssociationMapping(array('fieldName' => 'category',
            'type' => \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY,
            'targetEntity' => $this->categoryAdmin->getClass(),
            'cascade'       => array(
                0 => 'persist',
            )));

        // TODO: add label on config

        return $formMapper->create('category', 'sonata_type_model_list', array(
            'sonata_field_description' => $fieldDescription,
            'class'                    => $this->categoryAdmin->getClass(),
            'model_manager'            => $this->categoryAdmin->getModelManager()),
            array('link_parameters' => array('context' => 'news', 'hide_context' => true))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(BlockInterface $block)
    {
        $block->setSetting('category', is_object($block->getSetting('category')) ? $block->getSetting('category')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(BlockInterface $block)
    {
        $block->setSetting('category', is_object($block->getSetting('category')) ? $block->getSetting('category')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        $category = $block->getSetting('category', null);

        if (is_int($category)) {
            $category = $this->categoryManager->findOneBy(array('id' => $category));
        }

        $block->setSetting('category', $category);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {

        $settings = $blockContext->getBlock()->getSettings('category');

        $parameters = array(
            'block_context'  => $blockContext,
            'settings'       => $blockContext->getSettings(),
            'block'          => $blockContext->getBlock(),
        );

        if(isset($settings['category']) && $settings['category'] instanceof CategoryInterface) {
            $pager = $this->categoryManager->getSubCategoryPager($settings['category']->getId());
            $pager->setMaxPerPage($this->maxPerPage ?: 5);
            $pager->setCurrentPage(1, false, true);

            $parameters['pager'] = $pager;
            $parameters['category'] = $settings['category'];
        }

        if ($blockContext->getSetting('mode') !== 'public') {
            return $this->renderPrivateResponse($blockContext->getTemplate(), $parameters, $response);
        }

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Category List';
    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'mode'       => 'public',
            'template'   => 'RzClassificationBundle:Block:category_list.html.twig',
            'ajaxTemplate'   => 'RzClassificationBundle:Block:category_ajax.html.twig',
            'ajaxPagerTemplate'   => 'RzClassificationBundle:Block:category_ajax_pager.html.twig',
            'category' => null,
        ));
    }
}
