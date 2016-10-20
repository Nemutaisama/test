<?php
/**
 * Created by PhpStorm.
 * User: ageev
 * Date: 10/30/14
 * Time: 5:14 PM
 */

namespace Tasks;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Watcher implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var Model\TaskManager
     */
    protected $taskManager = null;


    public function __construct(ServiceLocatorInterface $sl = null) {
        if (!is_null($sl)) {
            $this->serviceLocator = $sl;
        }
    }

    /**
     * @param ServiceLocatorInterface $sl
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $sl) {
        $this->serviceLocator = $sl;

        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events) {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('*', 'task.add', array($this, 'addTask'), 100);
    }

    public function detach(EventManagerInterface $events) {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @return Model\TaskManager
     */
    public function getTaskManager() {
        if (null === $this->taskManager) {
            $this->taskManager = $this->getServiceLocator()->get('TaskManager');
        }

        return $this->taskManager;
    }

    public function addTask($e) {
        return $this->getTaskManager()->addTask($e->getParams());
    }

}