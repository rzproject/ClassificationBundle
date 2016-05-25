<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\ContextManager as BaseContextManager;

class ContextManager extends BaseContextManager
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

    /**
     * @param array $contexts
     * @param bool $enabled
     *
     * @return Array
     */
    public function getDefunctContext(array $contexts, $enabled = true)
    {
        $query = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from( $this->getClass(), 'c')
            ->where('c.id NOT IN (:context)')
            ->andWhere('c.enabled = :enabled')
            ->setParameter('context', $contexts)
            ->setParameter('enabled', $enabled)
            ->getQuery()
            ->useResultCache(true, 3600);

        $defunctContexts = $query->getResult();

        return $defunctContexts;
    }

    public function generateDefaultContext($code='default', $name = null, $enabled = true)
    {
        if(!$name) {
            $code = $this->getSlugify()->slugify($code);
            $name = ucwords(str_replace('-', ' ',$code));
        }
        $id = $this->getSlugify()->slugify($name);
        $context = $this->create();
        $context->setEnabled($enabled);
        $context->setId($code);
        $context->setName(substr($name,0,255));
        $this->save($context);
        return $context;
    }
}
