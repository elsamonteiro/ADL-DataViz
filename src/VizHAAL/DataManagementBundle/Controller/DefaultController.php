<?php

namespace VizHAAL\DataManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Exception\RuntimeException;


class DefaultController extends Controller
{
    // Transform data by connecting event into one word (Deliminator is "-")
    public function transformAction()
    {
        $path = "/Applications/XAMPP/xamppfiles/htdocs/S5/src/VizHAAL/DataManagementBundle/Resources/data";
        $fileName = "Kasteren_HouseA_activities_T0.txt";
        $this->transform($path,$fileName);
        return $this->render('VizHAALDataManagementBundle:Default:transform.html.twig');
    }

    // Import activities' data from file
    public function importAction(Request $request)
    {
        // Get all patients in DB
        $patientArr = $this->getAllPatients();
        $patientNames = array();
        foreach($patientArr as $patient){
            $patientNames[$patient->getID()] = $patient->getName()." ".$patient->getLastName();
        }
        $form = $this->createFormBuilder()
            ->add('patient', 'choice', array(
                'choices'   => $patientNames,
                'required'  => true
            ))
            ->add('importFile', 'file', array(
                'label' => 'File to import',
                'required' => true
            ))
            ->add('submit','submit')
            ->getForm();

        // Verify whether the request came from the submit button or not
        if ($request->getMethod('post') == 'POST') {
            // Bind request to the form
            $form->submit($request);
            // If form is valid
            if ($form->isValid()) {
                // Get file
                $file = $form->get('importFile');
                // Get patient name
                $patientId = $form->get('patient')->getData();
                // Delete the exist Database
                $this->dropDatasetTable($patientId);
                //Create a table by using the patientId
                $this->createDatasetTable($patientId);
                // Get data inside the file
                $this->importDataset($file->getData(),$patientId);
            }

        }

        return $this->render('VizHAALDataManagementBundle:Default:import.html.twig',
            array('form' => $form->createView(),)
        );
    }

    // Transform data by connecting event into one word (Deliminator is "-")
    public function transform($file, $separator)
    {
        $dataTransformedArr = array();
        $handle = fopen($file, "r") or die("Couldn't open $file");
        // Read data line by line
        while (($line = fgets($handle)) !== false) {
            $dataTransformedArr[] = explode($separator,$line);
//            $dataTransformed = array();
//            // Split each line by space
//            $dataArray = explode(" ", $line);
//            // Put first and second words
//            $dataTransformed[] = $dataArray[0];
//            $dataTransformed[] = $dataArray[1];
//            // Add hyphen between words for combining an activity into one word
//            for($i=2;$i<count($dataArray)-1;$i++){
//                // Don't append hyphen for the last word of activity
//                if($i==count($dataArray)-2){
//                    $dataTransformed[]= $dataArray[$i];
//                }
//                else{
//                    $dataTransformed[] = $dataArray[$i];
//                }
//            }
//            // Put the last word
//            $dataTransformed[] = $dataArray[count($dataArray)-1];
//            // Put each link in array
//            $dataTransformedArr[] = $dataTransformed;
            // Write the data
           // file_put_contents($fileWrite, $dataTransformed, FILE_APPEND | LOCK_EX);
        }
        fclose($handle);
        return $dataTransformedArr;
    }

    // Import file into Database
    public function importDataset($file,$patientId){
        $dataTransformedArr = $this->transform($file, " ");
        try {
            // Traverse each line of the Dataset
            for ($i = 0; $i < sizeof($dataTransformedArr); $i += 2) {
                $startEvent = $dataTransformedArr[$i];
                $endEvent = $dataTransformedArr[$i + 1];
                $this->insertDataset($startEvent, $endEvent, $patientId);
            }
        } catch (Exception $e) {
            echo 'Cannot import the dataset, make sure that all the events has both start and end';
        }

    }

    // Create a new table of Dataset
    public function createDatasetTable($patientId){
        $sql = "
            CREATE TABLE DATA_".$patientId."
            (
            id int NOT NULL AUTO_INCREMENT,
            event varchar(100) NOT NULL,
            begin datetime,
            end datetime,
            place varchar(100),
            PRIMARY KEY (ID)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->execute();
    }

    // Insert dataset into a given table name
    public function insertDataset($startEvent,$endEvent,$patientId){
        // In case of three truncated events, "?" operation is used to verify it
        $event = $startEvent[2].'-'.$startEvent[3].(sizeof($startEvent)==6 ? '-'.$startEvent[4] : "");
        $sql = "
            INSERT INTO DATA_".$patientId." (event,begin,end)
            VALUES (:event,:begin,:end);
        ";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->bindValue('event',$event);
        $stmt->bindValue('begin', $startEvent[0].' '.$startEvent[1],\PDO::PARAM_STR);
        $stmt->bindValue('end', $endEvent[0].' '.$endEvent[1],\PDO::PARAM_STR);
        $stmt->execute();
    }

    //
    public function dropDatasetTable($patientId){
        $sql = "DROP TABLE IF EXISTS DATA_" . $patientId . ";";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->execute();
    }

    // Get all patient objects
    public function getAllPatients(){
        $em = $this->getDoctrine()->getManager();
        // In the many-to-many case, you must
        // join your own attribute, roles, in the class
        // NOTE: "roles" is an attribute of the User class
        // NOT a column is the database.
        $query = $em->createQuery(
           'SELECT u
            FROM VizHAALVisplatBundle:User u
            INNER JOIN u.roles r
            WHERE r.name = :roleName
            ORDER BY u.name ASC'
        )->setParameter('roleName', 'Patient');

        $patientArr = $query->getResult();


        if (!$patientArr) {
            throw new RuntimeException(
                'There is no patient in the database'
            );
        }
        return $patientArr;
    }

        // Import home and sensors' data from files
    public function homeImportAction(Request $request)
    {
        // Get all patients in DB
        $patientArr = $this->getAllPatients();
        $patientNames = array();
        foreach($patientArr as $patient){
            $patientNames[$patient->getID()] = $patient->getName()." ".$patient->getLastName();
        }
        $form = $this->createFormBuilder()
            ->add('patient', 'choice', array(
                'choices'   => $patientNames,
                'required'  => true
            ))
            ->add('importMap', 'file', array(
                'label' => 'Home Map to import',
                'required' => true
            ))
            ->add('importSensors', 'file', array(
                'label' => 'Sensors Location to import',
                'required' => true
            ))
            ->add('importSensorsData', 'file', array(
                'label' => 'Sensors Data to import',
                'required' => true
            ))
            ->add('submit','submit')
            ->getForm();

        // Verify whether the request came from the submit button or not
        if ($request->getMethod('post') == 'POST') {
            // Bind request to the form
            $form->submit($request);
            // If form is valid
            if ($form->isValid()) {
                // Get file
                $map = $form->get('importMap');
                $sensors = $form->get('importSensors');
                $sensorsData = $form->get('importSensorsData');                
                // Get patient name
                $patientId = $form->get('patient')->getData();
                $mapName = "homeMap" . $patientId . "." . $map->getData()-> guessExtension();
                $mapDirectory = $this->get('kernel')->getRootDir() . "/../src/VizHAAL/DataManagementBundle/Resources/data";
                $mapUrl = $mapDirectory . "/" . $mapName;
                $map->getData()->move($mapDirectory,$mapName);
                // Delete the exist Database
                $this->dropHomeDatasetTable($patientId);
                //Create a table by using the patientId
                $this->createHomeDatasetTable($patientId);
                // Get data inside the file
                $this->importHomeDataset($mapUrl,$sensors->getData(), $sensorsData->getData(),$patientId);
            }

        }

        return $this->render('VizHAALDataManagementBundle:Default:home_import.html.twig',
            array('form' => $form->createView(),)
        );
    }

    // Import file into Database
    public function importHomeDataset($mapUrl,$sensors,$sensorsData,$patientId){
        // insert map url into table
        $this->insertMap($mapUrl,$patientId);
        $dataTransformedArr = $this->transform($sensors, "\t");
        try {
            // Traverse each line of the Dataset
            for ($i = 0; $i < sizeof($dataTransformedArr); $i += 1) {
                $sensor = $dataTransformedArr[$i];
                $this->insertSensor($sensor, $patientId);
            }
        } catch (Exception $e) {
            echo 'Cannot import the dataset, make sure the sensor location file is correct';
        }
        $dataTransformedArr2 = $this->transform($sensorsData, "\t");
        try {
            // Traverse each line of the Dataset
            for ($i = 1; $i < sizeof($dataTransformedArr2); $i += 1) {
                $sensorData = $dataTransformedArr2[$i];
                $this->insertSensorDataset($sensorData, $patientId);
            }
        } catch (Exception $e) {
            echo 'Cannot import the dataset, make sure the sensors data file is correct';
        }

    }

    // Create a new table of Dataset
    public function createHomeDatasetTable($patientId){
        $sql = "
            CREATE TABLE SENSOR_".$patientId."
            (
            id int NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            xposition integer,
            yposition integer,
            PRIMARY KEY (ID),
            INDEX ind_name (name(10))
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $sql2 = "
            CREATE TABLE SENSORDATA_".$patientId."
            (
            id int NOT NULL AUTO_INCREMENT,
            sensor_id int,
            begin datetime,
            end datetime,
            PRIMARY KEY (id),
            FOREIGN KEY (sensor_id) REFERENCES SENSOR_".$patientId."(id)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ";
        $stmt2 = $this->getDoctrine()->getManager()->getConnection()->prepare($sql2);
        $stmt2->execute();
    }

    // Insert dataset into a given table name
    public function insertMap($mapUrl,$patientId){
        $sql = "
            INSERT INTO Map (url,patientId_id)
            VALUES (:mapurl,:patient);
        ";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->bindValue('mapurl',$mapUrl, \PDO::PARAM_STR);
        $stmt->bindValue('patient',intval($patientId));
        $stmt->execute();
    }

        // Insert dataset into a given table name
    public function insertSensor($sensor,$patientId){
        $sql = "
            INSERT INTO SENSOR_".$patientId." (name,xposition,yposition)
            VALUES (:name,:xposition,:yposition);
        ";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->bindValue('name',$sensor[0]);
        $stmt->bindValue('xposition',intval($sensor[1]),\PDO::PARAM_INT);
        $stmt->bindValue('yposition', intval($sensor[2]),\PDO::PARAM_INT);
        $stmt->execute();
    }

        // Insert dataset into a given table name
    public function insertSensorDataset($sensorData,$patientId){
        $sql = "
            INSERT INTO SENSORDATA_".$patientId." (sensor_id,begin,end)
            VALUES ((SELECT id from SENSOR_".$patientId." WHERE name=:sensor),:begin,:end);
        ";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->bindValue('sensor', substr($sensorData[2], 0, -1));
        $stmt->bindValue('begin', $sensorData[0],\PDO::PARAM_STR);
        $stmt->bindValue('end', $sensorData[1],\PDO::PARAM_STR);
        $stmt->execute();
    }

    //
    public function dropHomeDatasetTable($patientId){
        $sql = "DROP TABLE IF EXISTS SENSORDATA_" . $patientId . ";
                DROP TABLE IF EXISTS SENSOR_" . $patientId . ";
                DELETE FROM Map WHERE patientId_id = " . $patientId . ";
        ";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
        $stmt->execute();
    }

}
