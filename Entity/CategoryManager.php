<?php


namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\CategoryManager as BaseCategoryManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Datagrid\PagerInterface;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;

use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class CategoryManager extends BaseCategoryManager
{


    public function getContexts() {
        $queryBuilder = $this->em->getRepository($this->class)->createQueryBuilder('c');
        $query = $queryBuilder
            ->select('c')
            ->groupBy('c.context')
            ->getQuery();

        return $query->getResult();
    }

}
