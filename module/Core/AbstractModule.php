<?php

namespace Core;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class AbstractModule
{
    protected $dir;
    protected $namespace;

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        $coreConfig = include __DIR__ . '/config/module.config.php';

        $main = include $this->dir . '/config/module.config.php';

        $last = [
            'doctrine' => array(
                'driver' => array(
                    $this->namespace . '_driver' => array(
                        'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                        'cache' => 'array',
                        'paths' => array($this->dir . '/src/' . $this->namespace . '/Entity')
                    ),
                    'orm_default' => array(
                        'drivers' => array(
                            $this->namespace . '\Entity' => $this->namespace . '_driver'
                        )
                    )
                ),
                'fixture' => array(
                    $this->namespace. '_fixture' => $this->dir . '/src/' . $this->namespace . '/Fixture',
                )
            ),

            'translator' => array(
                'translation_file_patterns' => array(
                    array(
                        'type'     => 'gettext',
                        'base_dir' => $this->dir . '/language',
                        'pattern'  => '%s.mo',
                    ),
                ),
            ),
        ];

        $last['asset_manager']['resolver_configs'] = [];
        $last['view_manager']['template_map'] = [];

        $template = ['core', 'admin'];

        foreach($template as $name){

            if(file_exists($this->dir . '/template/' . $name . '/template_map.php')){
                $last['view_manager']['template_map'] = array_merge(
                    $last['view_manager']['template_map'],
                    include $this->dir . '/template/' . $name . '/template_map.php'
                );
            }

            if(!empty($coreConfig['core'][$name . '_template'])){
                if($this->namespace == 'Core'){
                    $last['asset_manager']['resolver_configs'] = array_merge_recursive(
                        $last['asset_manager']['resolver_configs'],
                        include PUBLIC_PATH . '/../template/' . $coreConfig['core'][$name . '_template'] . '/file_map.php'
                    );
                }
                $last['view_manager']['template_map'] = array_merge(
                    $last['view_manager']['template_map'],
                    include PUBLIC_PATH . '/../template/' . $coreConfig['core'][$name . '_template'] . '/template_map.php'
                );
            } else {
                if(file_exists($this->dir . '/template/' . $name . '/file_map.php')) {
                    $last['asset_manager']['resolver_configs'] = array_merge_recursive(
                        $last['asset_manager']['resolver_configs'],
                        $last['asset_manager']['resolver_configs'] = include $this->dir . '/template/' . $name . '/file_map.php'
                    );
                   // print_r(include $this->dir . '/../template/' . $name . '/file_map.php');
                }
            }
        }

        return array_merge_recursive($main, $last);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->namespace => $this->dir . '/src/' . $this->namespace,
                ),
            ),
        );
    }
}
