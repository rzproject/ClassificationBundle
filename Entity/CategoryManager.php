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
use Rz\ClassificationBundle\Permalink\PermalinkInterface;

class CategoryManager extends BaseCategoryManager
{


    /**
     * @var \Rz\ClassificationBundle\Permalink\PermalinkInterface
     */
    protected $permalinkGenerator;

    public function setPermalinkGenerator(PermalinkInterface $permalinkGenerator)
    {
        $this->permalinkGenerator = $permalinkGenerator;
    }

    public function getPermalinkGenerator()
    {
        return $this->permalinkGenerator;
    }

    /**
     * Returns a category with the given permalink
     *
     * @param string $permalink
     *
     * @return CategoryInterface
     */
    public function getCategoryByPermalink($permalink)
    {
        try {
            $repository = $this->getRepository();
            $query = $repository->createQueryBuilder('p');

            $urlParameters = $this->getPermalinkGenerator()->getParameters($permalink);

            $parameters = array();

            if (isset($urlParameters['slug'])) {
                $query->andWhere('p.slug = :slug');
                $parameters['slug'] = $urlParameters['slug'];
            }

            if (count($parameters) == 0) {
                return null;
            }

            $query->setParameters($parameters);

            return $query->getQuery()->getSingleResult();

        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


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

    /**
     * @param integer $categoryId
     * @param array $criteria
     *
     * @return PagerInterface
     */
    public function getSubCategories($categoryId, $criteria = array())
    {
        $queryBuilder = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.parent = :categoryId')
            ->setParameter('categoryId', $categoryId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getCategoriesByIds($ids) {

        $queryBuilder = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.id in (:ids)')
            ->setParameter('ids', $ids);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getCategoriesByContextQuery($context) {
        $queryBuilder = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.context = :context')
            ->setParameter('context', $context);

        return $queryBuilder->getQuery();
    }

    public function getSubCategoriesByContextExceptRoot($context, $criteria = null) {

        $parameters['context'] = $context;
        $queryBuilder = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.context = :context')
            ->andWhere('c.parent IS NOT null');

        if (isset($criteria['category'])) {
            $queryBuilder->andWhere('c.id != :category');
            $parameters['category'] = $criteria['category'];
        }

        $queryBuilder->setParameters($parameters);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFirstSubCategoryByContext($context) {

        $parameters['context'] = $context;
        $queryBuilder = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.context = :context')
            ->andWhere('c.parent IS NOT null')
            ->setMaxResults(1)
            ->setParameters($parameters);

        return $queryBuilder->getQuery()->getSingleResult();
    }
}
