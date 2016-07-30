<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\CoreBundle\Validator\ErrorElement;
use Rz\CoreBundle\Provider\BasePool;

abstract class Pool extends BasePool
{
    public function addContext($name, $provider = null)
    {
        $this->addGroup($name, $provider);
    }

    public function hasContext($name)
    {
        return $this->hasGroup($name);
    }

    public function getContext($name)
    {
        return $this->getGroup($name);
    }

    public function getContexts()
    {
        return $this->getGroups();
    }

    public function getDefaultContext()
    {
        return $this->getDefaultGroup();
    }

    public function getProviderNameByContext($name)
    {
        return $this->getProviderNameByGroup($name);
    }
}
