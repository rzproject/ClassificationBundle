<?php

namespace Rz\ClassificationBundle\Permalink;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\Tag;

class CategoryPermalink implements PermalinkInterface
{
    protected $pattern;

    /**
     * @param $pattern
     */
    public function __construct($pattern = '%s')
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(CategoryInterface $category)
    {
        return sprintf($this->pattern, $this->createSubCategorySlug($category));
    }

    public function createSubCategorySlug(CategoryInterface $category)
    {
        $context = Tag::slugify($category->getContext()->getId());

        $categorySlug = sprintf('%s', $category->getSlug());
        while($parent = $category->getParent()) {
            if($parent->getSlug() != $context) {
                $categorySlug = sprintf('%s/', $parent->getSlug()) . $categorySlug;
            }
            $category = $parent;
        }

        return $categorySlug;
    }

    /**
     * @param string $permalink
     *
     * @return array
     */
    public function getParameters($permalink)
    {
        $parameters = explode('/', $permalink);

        if (count($parameters) == 0) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        if (false === strpos($permalink, '/')) {
            $slug = $permalink;
        } else {
            // always return the last slugs (category slug)
            $slug = array_pop($parameters);
        }

        return array(
            'slug'     => $slug
        );
    }

    /**
     * @param string $permalink
     *
     * @return array
     */
    public function getSlugParameters($permalink)
    {
        $parameters = explode('/', $permalink);

        if (count($parameters) == 0) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        if (false === strpos($permalink, '/')) {
            $slug = $permalink;
        } else {
            $slug = $parameters;
        }

        return $slug;
    }

    public function validatePermalink(CategoryInterface $category, $permalink)
    {
        return ($permalink == $this->generate($category));
    }
}
