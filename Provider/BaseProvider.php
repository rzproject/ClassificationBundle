<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\PostInterface;

abstract class BaseProvider implements ClassificationProviderInterface
{
    protected $templates = array();
    protected $metatagChoices = array();

    /**
     * @param string                                           $name
     */
    public function __construct($name)
    {
        $this->name          = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplates(array $templates)
    {
        $this->templates = $templates;
    }

    /**
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateChoices()
    {
        $list = array();
        foreach($this->templates as $key=>$value) {
            $list[$value['path']] = $value['name'].' - '.$value['path'];
        }
        return $list;
    }

    public function getTemplatePath($name)
    {
        $template = $this->getTemplate($name);
        if($template) {
            return $template['path'];
        } else {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        return isset($this->templates[$name]) ? $this->templates[$name] : null;
    }

    /**
     * @return array
     */
    public function getMetatagChoices()
    {
        return $this->metatagChoices;
    }

    /**
     * @param array $metatagChoices
     */
    public function setMetatagChoices($metatagChoices)
    {
        $this->metatagChoices = $metatagChoices;
    }
}
