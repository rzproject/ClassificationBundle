<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\CollectionManager as BaseManager;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

use Sonata\DatagridBundle\Pager\Doctrine\pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class CollectionManager extends BaseManager
{
    /**
     * {@inheritdoc}
     */
    public function findAllExcept($parameters)
    {
        $queryBuilder = $this->em->getRepository($this->class)->createQueryBuilder('c');
        $query = $queryBuilder
            ->select('c')
            ->where('c.id != :id')
            ->andWhere('c.context = :context')
            ->getQuery()
            ->useResultCache(true, 3600);

        $query->setParameters($parameters);

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByContextId($contextId)
    {
        $queryBuilder = $this->em->getRepository($this->class)->createQueryBuilder('c');
        $query = $queryBuilder
            ->select('c')
            ->where('c.context = :context')
            ->getQuery()
            ->useResultCache(true, 3600);

        $query->setParameters(array('context'=>$contextId));

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByConextAndSlug($context, $slug)
    {
        $queryBuilder = $this->em->getRepository($this->class)->createQueryBuilder('col');
        $query = $queryBuilder
            ->select('col')
            ->leftJoin('col.context', 'con')
            ->where('col.slug = :slug')
            ->andWhere('con.id = :context')
            ->setMaxResults(1)
            ->getQuery()
            ->useResultCache(true, 3600);

        $query->setParameters(array('context'=>$context, 'slug'=>$slug));

        return $query->getSingleResult();
    }
}
