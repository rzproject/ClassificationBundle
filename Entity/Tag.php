<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseTag;


abstract class Tag extends BaseTag
{
    protected $settings;

    public function __construct(){
        $this->enabled = true;
    }

    /**
     * {@inheritDoc}
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * {@inheritDoc}
     */
    public function setSetting($name, $value)
    {
        $this->settings[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }
}