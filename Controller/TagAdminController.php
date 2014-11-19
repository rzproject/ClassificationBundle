<?php

namespace Rz\ClassificationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;


class TagAdminController extends Controller
{

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {

        $currentContext = false;
        if ($context = $this->get('request')->get('context')) {
            $currentContext = $this->getContextManager()->find($context);
        }

        if (!$currentContext) {
            $contexts = $this->getContextManager()->findAll();
            $currentContext = current($contexts);
        } else {
            $contexts = $this->getContextManager()->findAllExcept(array('id'=>$currentContext->getId()));
        }

        $datagrid = $this->admin->getDatagrid();


        if ($this->admin->getPersistentParameter('context')) {
            $datagrid->setValue('context', ChoiceType::TYPE_EQUAL, $this->admin->getPersistentParameter('context'));
        } else {
            $datagrid->setValue('context', ChoiceType::TYPE_EQUAL, $currentContext->getId());
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action'     => 'list',
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'current_context'  => $currentContext,
            'contexts'         =>$contexts,
        ));
    }


    public function getContextManager() {
        return $this->get('sonata.classification.manager.context');
    }
}