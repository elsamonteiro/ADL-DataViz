<?php

namespace VizHAAL\VisplatBundle\Graph;

/**
 * Class GraphChart
 * @package VizHAAL\VisplatBundle\Graph
 */
class GraphChart
{
    /**
     * Create a pie chart data
     * @param $data all events from DB
     * @return  JSON data
     */
    public static function createPieChart($data, $days)
    {

// total as 24 hours (in seconds) // has to be changed if change of scale (week, month)

        $total = 24 * 3600 * $days;

        for ($i = 0; $i < sizeof($data); $i++) {
            $data[$i]['Duration'] = $data[$i]['Time'] / $total;
        }

// time spent we don't know how

        $rest = 0;
        $amount = 0;
        $max = sizeof($data);
        for ($i = 0; $i < $max; $i++) {
            $amount = $amount + $data[$i]['Time'];
        }
        $rest = $total - $amount;


        $data[$max]['Event'] = "unknown";
        $data[$max]['Begin'] = "0000-00-00 00:00:00";
        $data[$max]['End'] = "0000-00-00 00:00:00";
        $data[$max]['Frequency'] = 1;
        $data[$max]['Time'] = $rest;
        $data[$max]['Duration'] = $rest / $total;

        return json_encode($data);
    }


    /**
     * Create a Gantt chart data
     * @param $data all events from DB
     * @return  JSON data
     */
    public static function createGanttChart($data)
    {

        $status = null;

        for ($i = 0; $i < sizeof($data); $i++) {
            $data[$i]['status'] = $status;
        }

        return json_encode($data);

    }

    /**
     * Create a chord diagram data
     * @param $data all events from DB
     * @return  Two dimensional arrays of Events and Matrix
     */
    public static function createChordDiagram($data, $distinctEvents)
    {
        // Two dimensional arrays counting occurred events
        $occur = array();
        $events = array();
        // Remove outer array
        foreach ($distinctEvents as $eachEvent) {
            $events[] = $eachEvent['taskName'];
        }
        foreach ($data as $datum) {
            $eventsTran[] = $datum['taskName'];
        }
        if (sizeof($data) < 2) {
            $matrix[] = array(1);
            return array('events' => $events, 'matrix' => $matrix);
        } else {
            // Initialize
            for ($i = 0; $i < sizeof($events); $i++) {
                for ($j = 0; $j < sizeof($events); $j++) {
                    $occur[$events[$i]][$events[$j]] = 0;
                }
            }
            // Get each array of events
            for ($i = 1; $i < sizeof($eventsTran); $i++) {
                // The first event and the second event
                $occur[$eventsTran[$i - 1]][$eventsTran[$i]] += 1;
                // Inversely
                $occur[$eventsTran[$i]][$eventsTran[$i - 1]] += 1;
            }
        }
        // Create matrix
        $matrix = array();
        for ($i = 0; $i < sizeof($events); $i++) {
            $row = array();
            for ($j = 0; $j < sizeof($events); $j++) {
                $row[] = $occur[$events[$i]][$events[$j]];

            }

            $matrix[] = $row;
        }
        return array('events' => $events, 'matrix' => $matrix);
    }
	
	
	/**
     * Create a status table
     * @param $data all events from DB
     * @return  JSON data
     */
    public static function createStatusTable($data)
    {
        return json_encode($data);
    }


    /**
     * Create a heat map data
     * @param $sensorEvents all sensors events from DB
     * @return  a three dimensional arrays
     */
    public static function createHeatMap($sensorsEvents)
    {
        foreach ($sensorsEvents as $eachEvent) {
            $subdata['x'] = $eachEvent['X'];
            $subdata['y'] = $eachEvent['Y'];
            $subdata['value'] = $eachEvent['Frequency'];
            $subdata2['AvgDuration'] = $eachEvent['Time'];
            $subdata2['Frequency'] = $eachEvent['Frequency'];
            $subdata2['Sensor'] = $eachEvent['name'];
            $data[] = $subdata;
            $dataDetails[] = $subdata2;
        }
        return array('data' => $data, 'details' => $dataDetails);
    }

    /**
     * Create circular heat chart data
     * @param $Events all events from DB
     * @return  a three dimensional arrays
     */
    public static function createCircularHeatChart($Events)
    {

        // get distincts events list and unique index
        $indexEvent = 0;
        $listeEvents[] = 0;
        foreach ($Events as $eachEvent) {
            if(!array_key_exists($eachEvent['taskName'], $listeEvents))
            {
                $listeEvents[$eachEvent['taskName']] = $indexEvent;
                $indexEvent += 1;
            }
        }

        // prepare data
        $data = array_fill(0, sizeof($listeEvents)*24, 0);
        foreach ($Events as $eachEvent) {
            $date = strtotime($eachEvent['startDate']);
            $dateIndex = intval(date('G', $date));
            $index1 = $listeEvents[$eachEvent['taskName']];
            $data[$index1*24+$dateIndex]+=1;
        }
        return array('labels' => array_flip($listeEvents), 'data' => $data);
    }

    /**
     * Create tree map data
     * @param $Events all events from DB
     * @return  JSON data
     */
    public static function createTreeMap($allEvents, $distinctEvents)
    {
        
        // initialize data with distincts events list
        $data = array();
        $data['name'] = "TreeMap";
        $data['children'] = array();
        foreach ($distinctEvents as $eachEvent) {
            $subdata = array();
            $subdata['name']= $eachEvent['taskName'];
            foreach ($distinctEvents as $eachEvent2) {
                $subdataname['name'] = $eachEvent['taskName'] . " -> " . $eachEvent2['taskName'];
                $subdataname['value'] = 0;
                $subdata['children'][] = $subdataname;
            }
            $data['children'][] = $subdata;
        }
        // get list of events
        $listDistinctEvents = array();
        foreach ($distinctEvents as $eachEvent) {
            $listDistinctEvents[] = $eachEvent['taskName'];
        }
        $listDistinctEvents = array_flip($listDistinctEvents);

        //$listEvents = array();
        //foreach ($allEvents as $event) {
        //    $listEvents[] = $event['taskName'];
        //}
        //prepare data
        for ($i = 0; $i < sizeof($allEvents)-1; $i++) {
            $data['children'][$listDistinctEvents[$allEvents[$i]['taskName']]]['children'][$listDistinctEvents[$allEvents[$i+1]['taskName']]]['value']+=1;
        }

        return json_encode($data);
    }
}
