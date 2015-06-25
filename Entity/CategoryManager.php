<?php


namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\CategoryManager as BaseCategoryManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Datagrid\PagerInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
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

            $query = $query->setParameters($parameters)
                  ->getQuery();

            return $query->useResultCache(true, 3600)
                         ->getSingleResult();

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

        $query->useResultCache(true, 3600);

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
     * @param array $sort
     * @param bool $sortResult
     *
     * @return PagerInterface
     */
    public function getSubCategories($categoryId, $criteria = array('enabled'=>true),$sort = array('createdAt'=>'DESC'), $sortResult = true)
    {
        $query = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.parent = :categoryId')
            ->andwhere('c.enabled = :enabled')
            ->setParameter('categoryId', $categoryId);

        if(array_key_exists('enabled',$criteria)) {
            $query->setParameter('enabled', $criteria['enabled']);
        }

        if($sort) {
            $count = 0;
            foreach($sort as $field=>$order) {
                if($count == 0) {
                    $query->orderBy(sprintf('c.%s', $field), $order);
                } else {
                    $query->addOrderBy(sprintf('c.%s', $field), $order);
                }
            }
        } else {
            $query->orderBy('c.name', 'ASC');
        }
        $query = $query->getQuery();
        $query->useResultCache(true, 3600);

        if($sortResult) {
            $temp = $query->getResult();
            $categories = array();
            if($temp) {
                foreach($temp as $cat) {
                    if($cat->getSetting('order')) {
                        $categories[$cat->getSetting('order')] = $cat;
                    } else {
                        $categories[] = $cat;
                    }
                }
            }
            if($categories){
                ksort($categories);
            }
            return $categories;
        } else {
            return $query->getResult();
        }
    }

    /**
     * @param integer $categoryId
     * @return PagerInterface
     *
     */
    public function getSubCategoryPager($categoryId)
    {
        $query = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.parent = :categoryId')
            ->orderBy('c.name', 'ASC')
            ->setParameter('categoryId', $categoryId)
            ->getQuery();

        $query->useResultCache(true, 3600);

        try {
            return new Pagerfanta(new DoctrineORMAdapter($query));
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getCategoriesByIds($ids) {

        $query = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.id in (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery();

        $query->useResultCache(true, 3600);

        return $query->getResult();
    }

    public function getCategoriesByContextQuery($context) {
        $query = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.context = :context')
            ->setParameter('context', $context)
            ->getQuery();

        $query->useResultCache(true, 3600);

        return $query;
    }

    public function getSubCategoriesByContextExceptRoot($context, $criteria = null) {

        $parameters['context'] = $context;

        $query = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.context = :context')
            ->andWhere('c.parent IS NOT null');

        if (isset($criteria['category'])) {
            $query->andWhere('c.id != :category');
            $parameters['category'] = $criteria['category'];
        }

        $query = $query->setParameters($parameters)
                       ->getQuery();

        $query->useResultCache(true, 3600);

        return $query->getResult();
    }

    public function getFirstSubCategoryByContext($context) {

        $parameters['context'] = $context;

        $query = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.context = :context')
            ->andWhere('c.parent IS NOT null')
            ->setMaxResults(1)
            ->setParameters($parameters)
            ->getQuery();

        $query->useResultCache(true, 3600);

        return $query->getSingleResult();
    }

    /**
     * Load all categories from the database, the current method is very efficient for < 256 categories
     *
     */
    protected function loadCategories(ContextInterface $context)
    {
        if (array_key_exists($context->getId(), $this->categories)) {
            return;
        }

        $class = $this->getClass();

        $categories = $this->getObjectManager()->createQuery(sprintf('SELECT c FROM %s c WHERE c.context = :context ORDER BY c.parent ASC', $class))
            ->setParameter('context', $context->getId())
            ->useResultCache(true, 3600)
            ->execute();

        if (count($categories) == 0) {
            // no category, create one for the provided context
            $category = $this->create();
            $category->setName($context->getName());
            $category->setEnabled(true);
            $category->setContext($context);
            $category->setDescription($context->getName());

            $this->save($category);

            $categories = array($category);
        }

        foreach ($categories as $pos => $category) {
            if ($pos === 0 && $category->getParent()) {
                throw new \RuntimeException('The first category must be the root');
            }

            if ($pos == 0) {
                $root = $category;
            }

            $this->categories[$context->getId()][$category->getId()] = $category;

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();

            if ($parent) {
                $parent->addChild($category);
            }
        }

        $this->categories[$context->getId()] = array(
            0 => $root
        );
    }

	public function parseCategoryIds(CategoryInterface $category, &$categories) {
		$categories[] = $category->getId();
		#TODO settings for news parent category
		if($category->getParent() && $category->getParent()->getSlug() !== 'news') {
			$this->parseCategoryIds($category->getParent(), $categories);
		}
	}
}
