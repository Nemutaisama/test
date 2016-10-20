<?php

namespace Tasks\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tasks
 * @ORM\Table(name="tasks")
 *
 * @ORM\Entity
 */
class Tasks
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="event_id", type="string", length=255, nullable=false)
     */
    private $eventId;

    /**
     * @var string
     * @ORM\Column(name="params", type="text", length=65535, nullable=true)
     */
    private $params;

    /**
     * @var integer
     * @ORM\Column(name="workers_count", type="integer", nullable=false)
     */
    private $workersCount;

    /**
     * @var integer
     * @ORM\Column(name="workers_limit", type="integer", nullable=false)
     */
    private $workersLimit;

    /**
     * @var \DateTime
     * @ORM\Column(name="started", type="datetime", nullable=true)
     */
    private $started;

    /**
     * @var \DateTime
     * @ORM\Column(name="finished", type="datetime", nullable=true)
     */
    private $finished;

    /**
     * @var integer
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set eventId
     *
     * @param string $eventId
     *
     * @return Tasks
     */
    public function setEventId($eventId) {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return string
     */
    public function getEventId() {
        return $this->eventId;
    }

    /**
     * Set params
     *
     * @param string $params
     *
     * @return Tasks
     */
    public function setParams($params) {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Set workersCount
     *
     * @param integer $workersCount
     *
     * @return Tasks
     */
    public function setWorkersCount($workersCount) {
        $this->workersCount = $workersCount;

        return $this;
    }

    /**
     * Get workersCount
     *
     * @return integer
     */
    public function getWorkersCount() {
        return $this->workersCount;
    }

    /**
     * Set workersLimit
     *
     * @param integer $workersLimit
     *
     * @return Tasks
     */
    public function setWorkersLimit($workersLimit) {
        $this->workersLimit = $workersLimit;

        return $this;
    }

    /**
     * Get workersLimit
     *
     * @return integer
     */
    public function getWorkersLimit() {
        return $this->workersLimit;
    }

    /**
     * Set started
     *
     * @param \DateTime $started
     *
     * @return Tasks
     */
    public function setStarted($started) {
        $this->started = $started;

        return $this;
    }

    /**
     * Get started
     *
     * @return \DateTime
     */
    public function getStarted() {
        return $this->started;
    }

    /**
     * Set finished
     *
     * @param \DateTime $finished
     *
     * @return Tasks
     */
    public function setFinished($finished) {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return \DateTime
     */
    public function getFinished() {
        return $this->finished;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Tasks
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }
}
