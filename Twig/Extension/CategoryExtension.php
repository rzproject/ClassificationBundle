<?php

namespace Rz\ClassificationBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;

class CategoryExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CmsManagerSelectorInterface
     */
    private $categoryManager;

    /**
     * @param RouterInterface  $router
     * @param ManagerInterface $tagManager
     * @param BlogInterface    $blog
     */
    public function __construct(RouterInterface $router, ManagerInterface $categoryManager)
    {
        $this->router     = $router;
        $this->categoryManager = $categoryManager;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('rz_classification_category_permalink', array($this, 'generatePermalink')),
            new \Twig_SimpleFunction('rz_classification_category_object', array($this, 'getCategoryObject'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'rz_classification_category';
    }

    /**
     * @param \Sonata\ClassificationBundle\Model\CategoryInterface $category
     *
     * @return string|Exception
     */
    public function generatePermalink(CategoryInterface $category)
    {
        return $this->categoryManager->getPermalinkGenerator()->generate($category);
    }

    public function getCategoryObject($id) {

        $object = null;

        if ($id) {
            $object =  $this->categoryManager->find($id);
        }

        return $object;
    }
}
