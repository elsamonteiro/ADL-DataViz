<?php

namespace VizHAAL\VisplatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Map
 */
class Map
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
    private $url;


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
     * @return Map
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
     * Set url
     *
     * @param string $url
     * @return Map
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
