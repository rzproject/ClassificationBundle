<?php


namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\TagInterface;
use Cocur\Slugify\Slugify;


class SlugGenerator
{
    protected $slugify;

    public function __construct() {
        $this->slugify = new Slugify();
    }

    public function generateCollectionSlug(CollectionInterface $collection, $delimeter = '_') {
        return $this->slugify->slugify($collection->getName(), $delimeter);
    }

    public function generateCategoryAlias(CategoryInterface $category) {
        return;
    }

    public function generateTagAlias(TagInterface $tag) {
        return;
    }
}