<?php

namespace VizHAAL\VisplatBundle\Repository;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
    /**
     * Fetch all events
     *
     * @return mixed, Array of events
     */
    public function findAllGroupByEvent($patientId, $startDate, $endDate)
    {
        $sql = "
            SELECT `Event`, `Begin`, `End`, COUNT(`Event`) AS Frequency,
            TIMESTAMPDIFF( SECOND, `Begin`, `End`) AS Time
            FROM `DATA_" . $patientId . "`
            WHERE DATE(`begin`)
            BETWEEN
            STR_TO_DATE(:startDate,'%d/%b/%Y')
            AND
            STR_TO_DATE(:endDate,'%d/%b/%Y')
            GROUP BY `Event`;
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch all events
     *
     * @return mixed, Array of events
     */
    public function findAllEvents($patientId, $startDate, $endDate)
    {
        $sql = "
            SELECT `Event` AS `taskName`,
			`Begin` AS `startDate`,
			`End` AS `endDate`
			FROM `DATA_" . $patientId . "`
			WHERE DATE(`begin`)
            BETWEEN
            STR_TO_DATE(:startDate,'%d/%b/%Y')
            AND
            STR_TO_DATE(:endDate,'%d/%b/%Y');
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch all distinct events
     *
     * @return mixed, Array of events
     */
    public function findDistinctEvents($patientId, $startDate, $endDate)
    {
        $sql = "
            SELECT DISTINCT `Event` AS `taskName`
			FROM `DATA_" . $patientId . "`
			WHERE DATE(`begin`)
            BETWEEN
            STR_TO_DATE(:startDate,'%d/%b/%Y')
            AND
            STR_TO_DATE(:endDate,'%d/%b/%Y')
            ORDER BY `taskName`;
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Fetch all patients of the current doctor
     *
     * @return mixed, Array of events
     */
    public function findPatientsOfDoctor($doctorId)
    {

        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT u.id, u.name
            FROM VizHAALVisplatBundle:USER u
            WHERE u.doctorId = :doctorId
            ORDER BY u.name '
        )->setParameter('doctorId', $doctorId);
        $patients = $query->getResult();
        if (!$patients) {
            return null;
        }

        return $patients;
    }

    /**
     * Fetch the first patient, order by name
     *
     * @param $doctorId
     * @return null or the first patient
     */
    public function findFirstPatientsOfDoctor($doctorId)
    {

        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT u.id, u.name
            FROM VizHAALVisplatBundle:USER u
            WHERE u.doctorId = :doctorId
            ORDER BY u.name'
        )->setParameter('doctorId', $doctorId)
            ->setMaxResults(1);
        $patient = $query->getResult();
        if (!$patient) {
            return null;
        }

        return $patient[0];
    }


    /**
     * Fetch all dates of a patient.
     * Date format dd/mm/Y
     *
     * @return mixed, Array of events
     */
    public function findAllEventDate($patientId)
    {
        $sql = "
            SELECT DISTINCT DATE_FORMAT(`begin`, '%d/%b/%Y') as date
			FROM `DATA_" . $patientId . "`
			ORDER BY `begin`;
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch first dates of a patient.
     * Date format dd/mm/Y
     *
     * @return mixed, the date
     */
    public function findFirstEventDate($patientId)
    {
        $sql = "
            SELECT DISTINCT DATE_FORMAT(`begin`, '%d/%b/%Y') as begin
			FROM `DATA_" . $patientId . "`
			ORDER BY begin
			LIMIT 1;
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]['begin'];
    }

	
	/**
	* Calculate last time activity was made
	*
	*/
	public function findLastTime($patientId, $startDate, $endDate)
    {
        $sql = "
            SELECT DISTINCT `event`, MIN(TIMESTAMPDIFF( SECOND, `End`, NOW())) AS `lastTime`
			FROM `DATA_" . $patientId . "` 
			WHERE DATE(`begin`)
            BETWEEN
            STR_TO_DATE(:startDate,'%d/%b/%Y')
            AND
            STR_TO_DATE(:endDate,'%d/%b/%Y')
			GROUP BY `event` 
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch all sensors events
     *
     * @return mixed, Array of events
     */
    public function findAllGroupBySensorEvent($patientId, $startDate, $endDate)
    {
        $sql = "
            SELECT `sensor_id`, COUNT(`sensor_id`) AS Frequency,
            `xposition` AS X, `yposition` AS Y, `name`,
            AVG(TIMESTAMPDIFF( SECOND, `begin`, `end`)) AS Time
            FROM `SENSORDATA_" . $patientId . "` AS sd
            RIGHT JOIN `SENSOR_" . $patientId . "` AS s
            ON sd.sensor_id = s.id  
            WHERE DATE(`begin`)
            BETWEEN
            STR_TO_DATE(:startDate,'%d/%b/%Y')
            AND
            STR_TO_DATE(:endDate,'%d/%b/%Y')
            GROUP BY `sensor_id`;
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch url of the map
     *
     * @return mixed, url string
     */
    public function findMap($patientId)
    {
        $sql = "
            SELECT `url`
            FROM `Map`
            WHERE patientId_id=". $patientId . ";
        ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]['url'];
    }
}
