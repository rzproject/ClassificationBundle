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

    /**
     * @param ContextInterface $context
     *
     * @return CategoryInterface
     */
    public function getRootCategory($context = null)
    {
        $context = $this->getContext($context);
        $this->loadCategories($context);
        return $this->categories[$context->getId()][0];
    }

    /**
     * @param $contextCode
     *
     * @return ContextInterface
     */
    private function getContext($contextCode)
    {
        
        if ($contextCode === null || $contextCode === '') {
            $contextCode = ContextInterface::DEFAULT_CONTEXT;
        }

        if ($contextCode instanceof ContextInterface) {
            return $contextCode;
        }

        $context = $this->contextManager->find($contextCode);

        if (!$context instanceof ContextInterface) {
            $context = $this->contextManager->create();

            $context->setId($contextCode);
            $context->setName(ucwords(strtolower(str_replace('_', ' ',$contextCode))));
            $context->setEnabled(true);

            $this->contextManager->save($context);
        }

        return $context;
    }
}
