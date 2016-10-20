<?php
namespace Tasks;

use Zend\Mvc\MvcEvent;
use Tasks\Model\TaskManager;

class Module
{

    public function onBootstrap(MvcEvent $e) {
        $e->getApplication()->getEventManager()->attach(new Watcher($e->getApplication()->getServiceManager()));
    }

    public function getConfig() {
        return include __DIR__.'/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/src/'.__NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'TaskManager' => function ($sm) {
                    $tasks = new TaskManager($sm);

                    return $tasks;
                },
            ),
        );
    }
}
