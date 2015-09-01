<?php

namespace Rz\ClassificationBundle\Permalink;

use Sonata\ClassificationBundle\Model\CategoryInterface;

interface PermalinkInterface
{
    /**
     * @param \Sonata\ClassificationBundle\Model\CategoryInterface $category
     */
    public function generate(CategoryInterface $category);

    /**
     * @param string $permalink
     *
     * @return array
     */
    public function getParameters($permalink);
}
