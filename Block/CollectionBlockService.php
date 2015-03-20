<?php

namespace Rz\ClassificationBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionBlockService extends BaseBlockService
{
    protected $collectionManager;
    protected $templates;
    /**
     * @param string          $name
     * @param EngineInterface $templating
     */
    public function __construct($name, EngineInterface $templating, CollectionManagerInterface $collectionManager, $templates)
    {
        $this->name       = $name;
        $this->templating = $templating;
        $this->collectionManager = $collectionManager;
        $this->templates = $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('mode', 'choice', array(
                    'choices' => array(
                        'public' => 'public',
                        'admin'  => 'admin'
                    )
                )),
                array('templates', 'choice', array(
                    'choices' => $this->templates)),
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
//    public function getStylesheets($media)
//    {
//        return array(
//            '/bundles/rznews/css/news_block.css'
//        );
//    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Collection Block';
    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'mode'       => 'public',
            'template'   => 'RzClassificationBundle:Block:collection_list.html.twig'
        ));
    }
}
