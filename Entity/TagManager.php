<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\TagManager as BaseTagManager;

class TagManager extends BaseTagManager
{
    public function geTagQueryForDatagrid($contexts)
    {
        $qb = $this->getRepository()->createQueryBuilder('t');
        $qb->select('t')
            ->andWhere($qb->expr()->in('t.context', $contexts));

        return $qb->getQuery();
    }
}
