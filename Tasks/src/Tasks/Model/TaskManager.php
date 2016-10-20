<?php
namespace Tasks\Model;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Tasks\Entity\Tasks;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

/**
 * Class TaskManager
 *
 * @package Tasks\Model
 */
class TaskManager implements ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager = null;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Predis\Client
     */
    protected $redisStorage = null;

    /**
     * @param ServiceLocatorInterface $sl
     */
    public function __construct(ServiceLocatorInterface $sl = null) {
        $this->serviceLocator = $sl;

        return $this;
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
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.tasks');
        }

        return $this->entityManager;
    }

    /**
     * @return \Predis\Client
     */
    public function getRedisStorage() {
        if (null === $this->redisStorage) {
            $this->redisStorage = $this->getServiceLocator()->get('RedisStorage');
        }

        return $this->redisStorage;
    }

    /**
     * @param  EventManagerInterface $eventManager
     *
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager() {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager(__CLASS__));
        }

        return $this->eventManager;
    }

    /**
     * @param array $options
     *
     * @return Tasks
     */
    public function addTask($options = array()) {
        $task = new Tasks();
        $task->setStatus(0);
        $task->setEventId($options['event']);
        $task->setParams(json_encode($options['params']));
        $task->setWorkersCount(0);
        $task->setWorkersLimit($options['workers']);
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush($task);
        $redisStorage = $this->getRedisStorage();
        $redisStorage->hmset($task->getId(), array(
            'status' => 0,
            'step'   => 0,
            'cur'    => 0,
            'max'    => 0,
        ));

        return $task;
    }

    /**
     * @param $taskId
     *
     * @return array
     */
    public function check($taskId) {
        $redisStorage = $this->getRedisStorage();

        return $redisStorage->hgetall($taskId);
    }

    /**
     * @return bool
     */
    public function start() {
        if ($this->startRunningTask()) {
            return true;
        }

        return $this->startWaitingTask();
    }

    /**
     * @return bool
     */
    private function startRunningTask() {
        $tasksRepo = $this->getEntityManager()->getRepository(Tasks::class);
        /**
         * @var Tasks $task
         */
        $task = $tasksRepo->findOneBy(array('status' => 1));
        if (!$task) {
            return false;
        }
        if ($task->getWorkersCount() < $task->getWorkersLimit()) {
            $this->runTask($task);
            $this->finishTask($task);
        }

        return true;
    }

    /**
     * @return bool
     */
    private function startWaitingTask() {
        $tasksRepo = $this->getEntityManager()->getRepository(Tasks::class);
        /**
         * @var Tasks $task
         */
        $task = $tasksRepo->findOneBy(array('status' => 0));
        if (!$task) {
            return false;
        }

        $this->getRedisStorage()->hset($task->getId(), 'status', 1);
        $task->setStatus(1);
        $task->setStarted(new \DateTime());
        $this->runTask($task);
        $this->finishTask($task);
    }

    /**
     * @param Tasks $task
     */
    private function runTask(Tasks $task) {
        $task->setWorkersCount($task->getWorkersCount() + 1);
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush($task);

        $params = array(
            'taskId'  => $task->getId(),
            'options' => json_decode($task->getParams()),
        );
        $this->getEventManager()->trigger($task->getEventId(), null, $params);

        $this->getEntityManager()->refresh($task);
        $task->setWorkersCount($task->getWorkersCount() - 1);
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush($task);
    }

    /**
     * @param Tasks $task
     */
    private function finishTask(Tasks $task) {
        if ($task->getWorkersCount() == 0) {
            $this->getRedisStorage()->hset($task->getId(), 'status', 2);
            $task->setStatus(2);
            $task->setFinished(new \DateTime());
            $this->getEntityManager()->persist($task);
            $this->getEntityManager()->flush($task);
        }
    }
}