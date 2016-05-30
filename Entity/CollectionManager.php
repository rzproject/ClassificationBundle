<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\CollectionManager as BaseCollectionManager;
use Sonata\ClassificationBundle\Model\ContextInterface;

class CollectionManager extends BaseCollectionManager
{

    protected $slugify;

    /**
     * @return mixed
     */
    public function getSlugify()
    {
        return $this->slugify;
    }

    /**
     * @param mixed $slugify
     */
    public function setSlugify($slugify)
    {
        $this->slugify = $slugify;
    }

    public function generateDefaultCollection(ContextInterface $context, $slug, $enabled = true)
    {
        $slug = $this->getSlugify()->slugify($slug);
        $name = ucwords(str_replace('-', ' ',$slug));

        //create collection
        $collection = $this->create();
        $collection->setContext($context);
        $collection->setName($name);
        $collection->setDescription($name);
        $collection->setEnabled($enabled);
        $this->save($collection);
        return $collection;
    }
}
