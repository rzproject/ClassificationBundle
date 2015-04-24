<?php

namespace Rz\ClassificationBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Class NewsController
 * @package Rz\NewsBundle\Controller
 */
class CategoryController extends Controller
{

    protected function getCategoryXhrResponse($request, $category, $block, $parameters) {
        $settings = $block->getSettings();
        $ajaxTemplate = isset($settings['ajaxTemplate']) ? $settings['ajaxTemplate'] : 'RzClassificationBundle:Block:category_ajax.html.twig';
        $templatePagerAjax = isset($settings['ajaxPagerTemplate']) ? $settings['ajaxPagerTemplate'] : 'RzClassificationBundle:Block:category_ajax_pager.html.twig';
        $html = $this->container->get('templating')->render($ajaxTemplate, $parameters);
        $html_pager = $this->container->get('templating')->render($templatePagerAjax, $parameters);
        return new JsonResponse(array('html' => $html, 'html_pager'=>$html_pager));
    }

    protected function getCategoryDataForView($request, $category, $block, $page = null) {

        $parameters = array('category' => $category);

        if($page) {
            $parameters['page'] = $page;
        }

        $pager = $this->fetchSubCategories($parameters);

        if ($pager->getNbResults() <= 0) {
            throw new NotFoundHttpException('Invalid URL');
        }

        return $this->buildParameters($pager, $request, array('category' => $category, 'block'=>$block));
    }

    protected function buildParameters($pager, $request, $parameters = array()) {

        return array_merge(array(
                'pager' => $pager,
                'blog'  => $this->get('sonata.news.blog'),
                'tag'   => false,
                'route' => $request->get('_route'),
                'route_parameters' => $request->get('_route_params'),
                'type'  => 'none')
            ,$parameters);
    }

    protected function verifyCategory($categoryId) {

        $category = $this->getCategoryManager()->findOneBy(array(
            'id' => $categoryId,
            'enabled' => true
        ));

        if (!$category) {
            return false;
        }

        if (!$category->getEnabled()) {
            return false;
        }

        return $category;
    }

    protected function verifyBlock($blockId) {

        $block = $this->get('sonata.page.manager.block')->findOneBy(array(
            'id' => $blockId,
        ));

        if (!$block) {
            return false;
        }

        if (!$block->getEnabled()) {
            return false;
        }

        return $block;
    }


    /**
     * @param Request $request
     * @param $categoryId
     * @param $blockId
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function categoryAjaxPagerAction(Request $request, $categoryId, $blockId, $page = 1) {

        if(!$category = $this->verifyCategory($categoryId)) {
            throw new NotFoundHttpException('Unable to find the collection');
        }

        if(!$block = $this->verifyBlock($blockId)) {
            throw new NotFoundHttpException('Unable to find the block');
        }

        //redirect to normal controller if not ajax
        if (!$request->isXmlHttpRequest()) {
            //TODO implement central pager for SEO purposes
            //return $this->redirect($this->generateUrl('rz_news_collection_pager', array('collection'=>$collection->getSlug(), 'page'=>$page)), 301);
        }

        try {
            $parameters = $this->getCategoryDataForView($request, $category, $block, $page);
        } catch(\Exception $e) {
            throw $e;
        }

        return $this->getCategoryXhrResponse($request, $category, $block, $parameters);
    }

    protected function fetchSubCategories(array $criteria = array()) {

        if(array_key_exists('page', $criteria)) {
            $page = $criteria['page'];
            unset($criteria['page']);
        } else {
            $page = 1;
        }

        $pager = $this->getCategoryManager()->getSubCategoryPager($criteria['category']);
        $pager->setMaxPerPage($this->container->hasParameter('rz_classification.settings.category_pager_max_per_page')?$this->container->getParameter('rz_classification.settings.category_pager_max_per_page'): 5);
        $pager->setCurrentPage($page, false, true);
        return $pager;
    }

    /**
     * @return \Sonata\NewsBundle\Model\PostManagerInterface
     */
    protected function getCategoryManager()
    {
        return $this->get('sonata.classification.manager.category');
    }

}