<?php
class SleepMeasurementsDB {
    
    // returns an array of all SleepMeasurement objects found in the database
    public static function getAllMeasurements() {
        $allMeasurements = array();
        
        try {
            $db = Database::getDB();
            $stmt = $db->prepare(
                "select userName, sleepID, duration, dateAndTime, notes, userID
                from Users join SleepMeasurements using (userID)");
            $stmt->execute();
            $str = '';
            foreach ($stmt as $row) {
                $measurement = new SleepMeasurement($row);
                if (!is_object($measurement) || $measurement->getErrorCount() > 0)
                    throw new PDOException('Failed to create valid sleep measurement:\n' . $str);
                
                $allMeasurements[] = $measurement;
            }
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        
        return $allMeasurements;
    }
    
    // returns an array of SleepMeasurement objects, sorted by date
    public static function getMeasurementsBy($type, $value, $order = 'desc') {
        $allowedOrders = array('asc', 'desc');
        $allowedTypes = array('userName', 'userID');
        $measurementsArray = array();
    
        try {
            if (!in_array($order, $allowedOrders))
                throw new Exception("$order is not an allowed order");
            if (!in_array($type, $allowedTypes))
                throw new PDOException("$type not allowed search criterion for sleep measurement");
            
            $db = Database::getDB();
            $stmt = $db->prepare(
                "select userName, sleepID, duration, dateAndTime, notes, userID
                from Users join SleepMeasurements using (userID)
                where ($type = :$type)
                order by dateAndTime $order");
            $stmt->execute(array(":$type" => $value));
            
            $str = '';
            foreach ($stmt as $row) {
                $measurement = new SleepMeasurement($row);
                if (!is_object($measurement) || $measurement->getErrorCount() > 0)
                    throw new PDOException('Failed to create valid sleep measurement:\n' . $str);
    
                $measurementsArray[] = $measurement;
            }
    
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    
        return $measurementsArray;
    }
}
?>