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

    public function generateCategorySlug(CategoryInterface $category, $delimeter = '_') {
        return $this->slugify->slugify($category->getName(), $delimeter);
    }

    public function generateTagSlug(TagInterface $tag, $delimeter = '_') {
        return;
    }

    public function generateSlug($str, $delimeter = '_') {
        return $this->slugify->slugify($str, $delimeter);
    }
}