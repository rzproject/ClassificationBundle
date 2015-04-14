<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\ClassificationBundle\Model\PostInterface;

abstract class BaseProvider implements ClassificationProviderInterface
{
    protected $templates = array();
    protected $ajaxTemplates = array();
    protected $ajaxPagerTemplates = array();
    protected $metatagChoices = array();
    protected $controllerEnabled = true;

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
     * @return array
     */
    public function getAjaxTemplates()
    {
        return $this->ajaxTemplates;
    }

    /**
     * @param array $ajaxTemplates
     */
    public function setAjaxTemplates($ajaxTemplates)
    {
        $this->ajaxTemplates = $ajaxTemplates;
    }

    /**
     * @return array
     */
    public function getAjaxPagerTemplates()
    {
        return $this->ajaxPagerTemplates;
    }

    /**
     * @param array $ajaxPagerTemplates
     */
    public function setAjaxPagerTemplates($ajaxPagerTemplates)
    {
        $this->ajaxPagerTemplates = $ajaxPagerTemplates;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateChoices($object = null)
    {
        return $this->getChoices($this->templates);
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
     * {@inheritdoc}
     */
    public function getAjaxTemplateChoices($object = null)
    {
        return $this->getChoices($this->ajaxTemplates);
    }

    public function getAjaxTemplatePath($name)
    {
        $ajaxTemplate = $this->getAjaxTemplate($name);
        if($ajaxTemplate) {
            return $ajaxTemplate['path'];
        } else {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAjaxTemplate($name)
    {
        return isset($this->ajaxTemplates[$name]) ? $this->ajaxTemplates[$name] : null;
    }


    /**
     * {@inheritdoc}
     */
    public function getAjaxPagerTemplateChoices($object = null)
    {
        return $this->getChoices($this->ajaxPagerTemplates);
    }

    public function getAjaxPagerTemplatePath($name)
    {
        $ajaxPagerTemplate = $this->getAjaxPagerTemplate($name);
        if($ajaxPagerTemplate) {
            return $ajaxPagerTemplate['path'];
        } else {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAjaxPagerTemplate($name)
    {
        return isset($this->ajaxPagerTemplates[$name]) ? $this->ajaxPagerTemplates[$name] : null;
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

    /**
     * @return boolean
     */
    public function isControllerEnabled()
    {
        return $this->controllerEnabled;
    }

    /**
     * @param boolean $controllerEnabled
     */
    public function setControllerEnabled($controllerEnabled)
    {
        $this->controllerEnabled = $controllerEnabled;
    }

    protected function getChoices($templates) {
        $list = array();
        foreach($templates as $key=>$value) {
                $list[$value['path']] = $value['name'].' - '.$value['path'];
        }
        return $list;
    }
}
