<?php

namespace Rz\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseCategory as BaseCategory;


class Category extends BaseCategory
{
    protected $content;
    protected $rawContent;
    protected $contentFormatter;

    public function __construct(){
        $this->enabled = false;
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
}