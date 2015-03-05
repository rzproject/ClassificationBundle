<?php

namespace Rz\ClassificationBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;

class CollectionExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CmsManagerSelectorInterface
     */
    private $collectionManager;

    /**
     * @param RouterInterface  $router
     * @param ManagerInterface $tagManager
     * @param BlogInterface    $blog
     */
    public function __construct(RouterInterface $router, ManagerInterface $collectionManager)
    {
        $this->router     = $router;
        $this->collectionManager = $collectionManager;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'rz_classification_collection_permalink'    => new \Twig_Function_Method($this, 'generatePermalink'),
            'rz_classification_collection_object'    => new \Twig_Function_Method($this, 'getCollectionObject'),
			'rz_classification_collection_object_by_slug'    => new \Twig_Function_Method($this, 'getCollectionObjectBySlug')
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
        return 'rz_classification_collection';
    }

    /**
     * @param \Sonata\ClassificationBundle\Model\CollectionInterface $collection
     *
     * @return string|Exception
     */
    public function generatePermalink(CollectionInterface $collection)
    {
        return $this->collectionManager->getPermalinkGenerator()->generate($collection);
    }

    public function getCollectionObject($id) {

        $object = null;

        if ($id) {
            $object =  $this->collectionManager->find($id);
        }

        return $object;
    }
	
	public function getCollectionObjectBySlug($slug='') {

        $object = null;

        if ($slug != '') {
            $object =  $this->collectionManager->findOneBy(array('slug' => $slug ));
        }

        return $object;
    }
	
}
