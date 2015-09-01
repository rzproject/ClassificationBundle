<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\ClassificationBundle\Entity\TagManager as BaseTagManager;
use Doctrine\Common\Collections\Selectable;

class TagManager extends BaseTagManager
{
	public function parseTagIds(Selectable $tags) {
		$tagIds = array();
		foreach($tags as $tag) {
			$tagIds[] = $tag->getId();
		}

		return $tagIds;
	}
}
