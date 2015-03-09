<?php

namespace VizHAAL\VisplatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensor
 */
class Sensor
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \VizHAAL\VisplatBundle\Entity\User
     */
    private $patientId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $xposition;

    /**
     * @var integer
     */
    private $yposition;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sensorsData;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set patientId
     *
     * @param integer $patientId
     * @return Sensor
     */
    public function setPatientId($patientId)
    {
        $this->patientId = $patientId;

        return $this;
    }

    /**
     * Get patientId
     *
     * @return integer 
     */
    public function getPatientId()
    {
        return $this->patientId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Sensor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set xposition
     *
     * @param integer $xposition
     * @return Sensor
     */
    public function setXposition($xposition)
    {
        $this->xposition = $xposition;

        return $this;
    }

    /**
     * Get xposition
     *
     * @return integer 
     */
    public function getXposition()
    {
        return $this->xposition;
    }

    /**
     * Set yposition
     *
     * @param integer $yposition
     * @return Sensor
     */
    public function setYposition($yposition)
    {
        $this->yposition = $yposition;

        return $this;
    }

    /**
     * Get yposition
     *
     * @return integer 
     */
    public function getYposition()
    {
        return $this->yposition;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sensorsData = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add sensorsData
     *
     * @param \VizHAAL\VisplatBundle\Entity\SensorData $sensorsData
     * @return Sensor
     */
    public function addSensorsDatum(\VizHAAL\VisplatBundle\Entity\SensorData $sensorsData)
    {
        $this->sensorsData[] = $sensorsData;

        return $this;
    }

    /**
     * Remove sensorsData
     *
     * @param \VizHAAL\VisplatBundle\Entity\SensorData $sensorsData
     */
    public function removeSensorsDatum(\VizHAAL\VisplatBundle\Entity\SensorData $sensorsData)
    {
        $this->sensorsData->removeElement($sensorsData);
    }

    /**
     * Get sensorsData
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSensorsData()
    {
        return $this->sensorsData;
    }
}
