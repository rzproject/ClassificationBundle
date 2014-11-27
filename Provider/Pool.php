<?php

namespace Rz\ClassificationBundle\Provider;

use Sonata\AdminBundle\Validator\ErrorElement;

abstract class Pool
{
    /**
     * @var array
     */
    protected $providers = array();

    protected $contexts = array();

    protected $defaultContext;

    /**
     * @param string $context
     */
    public function __construct($context)
    {
        $this->defaultContext = $context;
    }

    /**
     * @throws \RuntimeException
     *
     * @param string $name
     *
     * @return \Sonata\MediaBundle\Provider\MediaProviderInterface
     */
    public function getProvider($name)
    {
        if (!isset($this->providers[$name])) {
            throw new \RuntimeException(sprintf('unable to retrieve the provider named : `%s`', $name));
        }

        return $this->providers[$name];
    }

    /**
     * @param array $providers
     *
     * @return void
     */
    public function setProviders($providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return \Rz\NewsBundle\Provider\PostProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param string $name
     * @param array $provider
     * @param null $defaultTemplate
     * @param array $templates
     *
     * @return void
     */
    public function addContext($name, $provider = null, $defaultTemplate = null, $templates = array())
    {
        if (!$this->hasContext($name)) {
            $this->collections[$name] = array(
                'default_template' => null,
                'provider' => null,
                'templates' =>array()
            );
        }

        $this->contexts[$name]['default_template'] = $defaultTemplate;
        $this->contexts[$name]['provider'] = $provider;
        $this->contexts[$name]['templates'] = $templates;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasContext($name)
    {
        return isset($this->contexts[$name]);
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function getContext($name)
    {
        if (!$this->hasContext($name)) {
            return null;
        }

        return $this->contexts[$name];
    }

    /**
     * Returns the context list
     *
     * @return array
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getProviderNameByContext($name)
    {
        $context = $this->getContext($name);

        if (!$context) {
            return null;
        }

        return $context['provider'];
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getTemplatesNameByContext($name)
    {
        $context = $this->getContext($name);

        if (!$context) {
            return null;
        }

        return $context['templates'];
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getDefaultTemplateNameByContext($name)
    {
        $context = $this->getContext($name);

        if (!$context) {
            return null;
        }

        return $context['default_template'];
    }

    /**
     * @param string $name
     * @param string $template
     *
     * @return boolean
     */
    public function hasTemplateByContext($name, $template = 'default')
    {
        $templates = $this->getTemplatesNameByContext($name);
        return isset($templates[$template]);
    }

    /**
     * @param string $name
     * @param string $template
     *
     * @return array
     */
    public function getTemplateByContext($name, $template = 'default')
    {
        $templates = $this->getTemplatesNameByContext($name);
        if($this->hasTemplateByContext($name, $template)) {
            $config = $templates[$template];
        } else {
            $config = array_pop($templates);
        }

        return $config;
    }


    /**
     * @return string
     */
    public function getDefaultContext()
    {
        return $this->defaultContext;
    }
}
