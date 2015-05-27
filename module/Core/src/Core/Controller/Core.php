<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Core\Controller;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Core\Traits\ServiceDoctrine;
use Core\Traits\ServiceOption;
use Core\Traits\ServiceResource;

class Core extends AbstractActionController
{
    use ServiceDoctrine;
    use ServiceOption;
    use ServiceResource;

    protected $view;


    protected function init(){}

    protected function addInlineScript($link)
    {
        $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript')->appendFile($this->getBasePath() . $link );
    }

    protected function addHeadScript($link)
    {
        $this->getServiceLocator()->get('viewhelpermanager')->get('headScript')->appendFile($this->getBasePath() . $link );
    }

    protected function getBasePath()
    {
        $basePathHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('basePath');
        return $basePathHelper('');
    }

    protected function initLink()
    {

    }

    protected function translate($message, $textDomain = null, $locale = null)
    {
        $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
        return $translate($message, $textDomain, $locale);
    }

    public function onDispatch(MvcEvent $e)
    {
        /*$timeZoneOption = $this->getOptions()->get('core', 'default_timezone');
        if($timeZoneOption->getId()){
            date_default_timezone_set($timeZoneOption->getValue());
        } else {
            date_default_timezone_set('Europe/Moscow');
        }*/

        $this->init($e);
        $layoutName = $this->layout()->getTemplate();
        $this->initLink();

        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');

        $this->view = new ViewModel();

        return parent::onDispatch($e);
    }

    /**
     * Shorcut for flash message
     *
     * @param string $message
     * @param string $namespace
     *
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function flash($message, $namespace = 'success')
    {
        $this
            ->flashMessenger()
            ->setNamespace($namespace)
            ->addMessage($this->translate($message));
    }

    /**
     * Shortcut for creating a view
     * The return of the view directly increases performance
     *
     * @param string $template - path to template without
     * @param array $variables
     * @param array $children
     *
     * @return ViewModel
     */
    protected function render($template, $variables = array(), $children = array())
    {
        $this->view->setTemplate($template);
        if (! empty($variables)) {
            $this->view->setVariables($variables);
        }
        if (! empty($children)) {
            foreach ($children as $key => $child) {
                $this->view->addChild($child, $key);
            }
        }
        return $this->view;
    }

    public function ilist($params)
    {
        $options = $this->params()->fromPost('options', []);
        $filters = $this->params()->fromPost('filters', []);

        if(isset($params['filters'])) $filters = array_merge($filters, $params['filters']);
        if(isset($params['options'])) $options = array_merge($options, $params['options']);

        $collection = $this->getModelCollection($params['module'], $params['model']);
        $collection->findByFilter($filters, $options);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = ['success' => false];
            $data['html'] = [];
            $data['html']['items'] = $this->partialHtml($params['template_main'] . '/items', ['collection' => $collection]);
            $data['success'] = true;
            $data['is_show_more'] = $collection->isShowMore();
            if(!empty($params['response'])) $data = array_merge($params['response'], $data);
            return new JsonModel($data);
        }
        $params['collection'] = $collection;
        return $this->render($params['template_main'], $params);
    }

    public function itable($params)
    {
        $page = $this->params()->fromPost('page', 1);

        $options = $this->params()->fromPost('options', []);
        $filters = $this->params()->fromPost('filters', []);

        $repository = $this->getServiceDoctrine()->getRepository($params['module'], $params['model']);

        if(isset($params['prepend_filters'])) $filters = array_merge($params['prepend_filters'], $filters);
        if(isset($params['prepend_options'])) $options = array_merge($params['prepend_options'], $filters);

        if(isset($params['filters'])) $filters = array_merge($filters, $params['filters']);
        if(isset($params['options'])) $options = array_merge($filters, $params['options']);
        $options['page'] = $page;

        if(isset($params['append_filters'])) $filters = array_merge($filters, $params['append_filters']);
        if(isset($params['append_options'])) $options = array_merge($filters, $params['append_options']);


        $collection = $repository->getPage($filters, $options);

        $params['template_paginator'] = empty($params['template_paginator']) ? 'core/block/pagination' : $params['template_paginator'];

        $request = $this->getRequest();
        if($request->isPost()){
            $data = ['success' => false];
            switch($this->params()->fromPost('action')){
                case 'delete':
                    $model = $this->getServiceDoctrine()->getEntity($params['module'], $params['model'], $this->params()->fromPost('object_id'));
                    $model->delete();
                    $data['success'] = true;
                    break;

                case 'create':
                    $form = $params['create_form'];
                    $form->setData($this->params()->fromPost());
                    if($form->isValid()){
                        $this->_create($form->getData());
                        $data['success'] = true;
                    } else {
                        $data['errors'] = $form->getMessages();
                    }
                    break;

                case 'load-edit':
                    $object = $this->getServiceDoctrine()->getEntity($params['module'], $params['model'], $this->params()->fromPost('object_id'));
                    $this->_loadEdit($object, $params['edit_form']);
                    $params['object'] = $object;
                    $data['html'] = $this->partialHtml($params['template_edit'], $params);
                    $data['success'] = true;
                    break;

                case 'edit':
                    $form = $params['edit_form'];
                    $form->setData($this->params()->fromPost());
                    if($form->isValid()){
                        $this->_edit($form->getData());
                        $data['success'] = true;
                    } else {
                        $data['errors'] = $form->getMessages();
                    }
                    break;

                default;
                    $params['collection'] = $collection;
                    $data['html'] = [];
                    $paginationControl = $this->getServiceLocator()->get('viewhelpermanager')->get('paginationHelper');
                    $data['html']['pagination'] = $paginationControl($collection, ['template' => $params['template_paginator']]);
                    $data['html']['table'] = $this->partialHtml($params['template_main'] . '/table', $params);
                    $data['success'] = true;
                    break;
            }
            if(!empty($params['response'])) $data = array_merge($params['response'], $data);
            return new JsonModel($data);
        } else {
            $params['collection'] = $collection;
            return $this->render($params['template_main'], $params);
        }
    }

    protected function _create($data){}
    protected function _loadEdit($object, $form){}
    protected function _edit($data){}

    protected function resourceViewPlus($resource)
    {
        $this->getServiceResource()->viewPlus($resource);
    }


    protected function partialHtml($template, $data)
    {
        $partial = $this->getServiceLocator()->get('viewhelpermanager')->get('partial');
        $html = $partial($template, $data);
        return $html;
    }

    protected function show404()
    {
        return $this->render('404');
    }
}
