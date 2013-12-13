<?php

namespace Rz\ClassificationBundle\Form\Type;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\CoreBundle\Entity\ManagerInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;


/**
 * Select a category
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class CategorySelectorType extends AbstractTypeExtension
{
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //* TODO: enable via config
        if (!$options['expanded'] && $options['multiple']) {
            $view->vars['select2'] = $options['select2'];
            //enable selectpicker by default
            if ($view->vars['select2']) {
                $view->vars['selectpicker_enabled'] = false;
            } elseif ($options['multiselect_enabled']) {
                $view->vars['selectpicker_enabled'] = false;
            } elseif ($options['multiselect_search_enabled']) {
                $view->vars['selectpicker_enabled'] = false;
            } else {
                $view->vars['selectpicker_enabled'] = $options['selectpicker_enabled'] ? $options['selectpicker_enabled'] : true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $that = $this;

         $resolver->setOptional(array('selectpicker_enabled',
                                     'selectpicker_data_style',
                                     'selectpicker_title',
                                     'selectpicker_selected_text_format',
                                     'selectpicker_show_tick',
                                     'selectpicker_data_width',
                                     'selectpicker_data_size',
                                     'selectpicker_disabled',
                                     'selectpicker_dropup',
                                     'select2',
                                     'chosen_data_placeholder',
                                     'chosen_no_results_text',
                                     'multiselect_enabled',
                                     'multiselect_search_enabled',
                                    )
                                );

        $resolver->setDefaults(array('compound' => function (Options $options) {
                                       return isset($options['expanded']) ? ($options['expanded'] ? true: false) : false;
                                     },
                                     'select2' => false,
                                     'selectpicker_enabled' => true,
                                     'multiselect_enabled' => false,
                                     'multiselect_search_enabled' => false,
                                     'error_bubbling'=> true,
                                     'category'          => null,
                                     'choice_list'       => function (Options $opts, $previousValue) use ($that) {
                                             return new SimpleChoiceList($that->getChoices($opts));
                                         }
                               )
        );
    }

    /**
     * @param Options $options
     *
     * @return array
     */
    public function getChoices(Options $options)
    {
        if (!$options['category'] instanceof CategoryInterface) {
            return array();
        }

        $root = $this->manager->getRootCategory();

        $choices = array();

        $this->childWalker($root, $options, $choices);

        return $choices;
    }

    /**
     * @param CategoryInterface $category
     * @param Options           $options
     * @param array             $choices
     * @param int               $level
     */
    private function childWalker(CategoryInterface $category, Options $options, array &$choices, $level = 1)
    {

        if($category->getChildren() === null ) {
            return;
        }

        foreach ($category->getChildren() as $child) {
            if ($options['category'] && $options['category']->getId() == $child->getId()) {
                continue;
            }

            $choices[$child->getId()] = sprintf("%s %s", str_repeat('-' , 1 * $level), $child);

            $this->childWalker($child, $options, $choices, $level + 1);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'sonata_category_selector';
    }
}
