<?php

namespace Rz\ClassificationBundle\Provider\Tag;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\ClassificationBundle\Model\TagInterface;
use Rz\ClassificationBundle\Provider\Tag\BaseProvider;

class DefaultProvider extends BaseProvider
{
    /**
     * @param FormMapper $formMapper
     * @param null $object
     * @return array
     */
    public function getFormSettingsKeys(FormMapper $formMapper, $object = null)
    {
        $settings = array(
            array('abstract', 'text', array('required' => false,)),
            array('content', 'sonata_formatter_type', function (FormBuilderInterface $formBuilder) {
                return array(
                    'event_dispatcher' => $formBuilder->getEventDispatcher(),
                    'format_field'     => array('format', '[format]'),
                    'source_field'     => array('rawContent', '[rawContent]'),
                    'target_field'     => '[content]',
                );
            }),
        );
        return $settings;
    }

    public function load(TagInterface $object) {}

    public function validate(ErrorElement $errorElement, TagInterface $object){}
}
