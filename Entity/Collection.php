<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseCollection;


abstract class Collection extends BaseCollection
{
    protected $content;
    protected $rawContent;
    protected $contentFormatter;
    protected $settings;
    protected $page;
    protected $hasPage;

    public function __construct(){
        $this->enabled = true;
        $this->hasPage = false;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $contentFormatter
     */
    public function setContentFormatter($contentFormatter)
    {
        $this->contentFormatter = $contentFormatter;
    }

    /**
     * @return mixed
     */
    public function getContentFormatter()
    {
        return $this->contentFormatter;
    }

    /**
     * @param mixed $rawContent
     */
    public function setRawContent($rawContent)
    {
        $this->rawContent = $rawContent;
    }

    /**
     * @return mixed
     */
    public function getRawContent()
    {
        return $this->rawContent;
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

    /**
     * {@inheritDoc}
     */
    public function getSetting($name, $default = null)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : $default;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getHasPage()
    {
        return $this->hasPage;
    }

    /**
     * @param mixed $hasPage
     */
    public function setHasPage($hasPage = false)
    {
        $this->hasPage = $hasPage;
    }
}