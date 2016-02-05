<?php

namespace Rz\ClassificationBundle\Controller;

use Sonata\ClassificationBundle\Controller\CategoryAdminController as Controller;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CategoryAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function treeAction(Request $request)
    {
        $currentContext = false;
        if ($context = $request->get('context')) {
            $currentContext = $this->getContextManager()->find($context);
        }

        if ($listMode = $this->getRequest()->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        if (!$currentContext) {
            $contexts = $this->getContextManager()->findAll();
            $currentContext = array_shift($contexts);
        } else {
            $contexts = $this->getContextManager()->findAllExcept(array('id'=>$currentContext->getId()));
        }

        $mainCategory   =  $this->get('sonata.classification.manager.category')->findOneBy(array('context'=>$currentContext, 'parent'=>null));

        $datagrid = $this->admin->getDatagrid();

        if ($this->admin->getPersistentParameter('context')) {
            $datagrid->setValue('context', ChoiceType::TYPE_EQUAL, $this->admin->getPersistentParameter('context'));
        } else {
            $datagrid->setValue('context', ChoiceType::TYPE_EQUAL, $currentContext->getId());
        }

        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('RzClassificationBundle:CategoryAdmin:tree.html.twig', array(
            'action'           => 'tree',
            'main_category'    => $mainCategory,
            'current_context'  => $currentContext,
            'form'             => $formView,
            'contexts'         =>$contexts,
            'csrf_token'       => $this->getCsrfToken('sonata.batch'),
        ));
    }

    /**
     * @internal param Request $request
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request = null)
    {
        $defaultContext = null;
        if(!$request->get('context')) {
            $defaultContext = $this->container->hasParameter('rz_classification.settings.category_admin_default_context') ? $this->container->getParameter('rz_classification.settings.category_admin_default_context') : 'default';
        }

        if ((!$request->get('filter') && !$request->get('filters')) || ($this->isXmlHttpRequest() && $request->get('mode') =='tree')) {
            if ($defaultContext) {
                return new RedirectResponse($this->admin->generateUrl('tree', array('context'=>$defaultContext)));
            } else {
                return new RedirectResponse($this->admin->generateUrl('tree'));
            }

        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $currentContext = false;
        if ($context = $request->get('context')) {
            $currentContext = $this->getContextManager()->find($context);
        }


        if (!$currentContext) {
            $contexts = $this->getContextManager()->findAll();
            $currentContext = current($contexts);
        } else {
            $contexts = $this->getContextManager()->findAllExcept(array('id'=>$currentContext->getId()));
        }

        $mainCategory   =  $this->get('sonata.classification.manager.category')->findOneBy(array('context'=>$currentContext, 'parent'=>null));

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
            'action'           => 'list',
            'main_category'    => $mainCategory,
            'current_context'  => $currentContext,
            'contexts'         =>$contexts,
            'form'             => $formView,
            'datagrid'         => $datagrid,
            'csrf_token'       => $this->getCsrfToken('sonata.batch'),
        ));
    }

    public function getContextManager() {
        return $this->get('sonata.classification.manager.context');
    }
}