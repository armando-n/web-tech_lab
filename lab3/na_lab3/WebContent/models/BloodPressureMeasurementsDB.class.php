<?php
class BloodPressureMeasurementsDB {
    
    // takes a BloodPressureMeasuremet object as its first argument. No other argument is required. A second argument
    // can be the userID, which reduces the amount of work necessary by 1 query
    public static function addMeasurement() {
        $measurementID = -1;
        $userID = -1;
    
        try {
            $db = Database::getDB();
            
            if (func_num_args() < 1)
                throw new PDOException('BloodPressureMeasurementsDB.addMeasurement: arguments expected');
            
            $measurement = func_get_arg(0);
            
            if (func_num_args() < 2) {
                $stmt = $db->prepare('select userID from Users where userName = :userName');
                $stmt->execute(array(":userName" => $measurement->getUserName()));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row !== false)
                    $userID = $row['userID'];
                else
                    throw new PDOException('User name "' . $measurement->getUserName() . '" not found');
            } else
                $userID = func_get_arg(1);
            
            $stmt = $db->prepare(
                "insert into BloodPressureMeasurements (systolicPressure,
                    diastolicPressure, dateAndTime, notes, userID)
                values (:systolicPressure, :diastolicPressure, :dateAndTime,
                    :notes, :userID)"
            );
            $stmt->execute(array(
                ":systolicPressure" => $measurement->getSystolicPressure(),
                ":diastolicPressure" => $measurement->getDiastolicPressure(),
                ":dateAndTime" => $measurement->getDateTime()->format("Y-m-d H:i"),
                ":notes" => $measurement->getNotes(),
                ":userID" => $userID
            ));
            $measurementID = $db->lastInsertId("bpID");
    
        } catch (PDOException $e) {
            $measurement->setError('bloodPressureMeasurementsDB', 'ADD_MEASUREMENT_FAILED');
        } catch (RuntimeException $e) {
            $measurement->setError('database', 'DB_CONFIG_NOT_FOUND');
        }
    
        return $measurementID;
    }
    
    public static function editMeasurement($oldMeasurement, $newMeasurement) {
        try {
            $db = Database::getDB();
            $oldParams = $oldMeasurement->getParameters();
            $newParams = $newMeasurement->getParameters();
            $numParams = count($oldMeasurement);
            $dateAndTime = $oldParams['dateAndTime'];
        
            foreach ($newParams as $key => $value) {
        
                if (!array_key_exists($key, $oldParams))
                    throw new PDOException('Key ' . htmlspecialchars($key) . ' is invalid');
        
                    if ($oldParams[$key] !== $newParams[$key]) {
                        $stmt = $db->prepare(
                                "update BloodPressureMeasurements set $key = :value
                                 where userID in
                                    (select userID from Users where userName = :userName)
                                 and dateAndTime = :dateAndTime");
                        $stmt->execute(array(
                            ":value" => $value,
                            ":userName" => $newParams['userName'],
                            ":dateAndTime" => $dateAndTime
                        ));
                    }
                    
                    if ($key === 'dateAndTime')
                        $dateAndTime = $value;
            }
        
        } catch (PDOException $e) {
            $newMeasurement->setError('bloodPressureMeasurementsDB', 'EDIT_MEASUREMENT_FAILED');
        } catch (RuntimeException $e) {
            $newMeasurement->setError('database', 'DB_CONFIG_NOT_FOUND');
        }
        
        return $newMeasurement;
    }
    
    // returns an array of all BloodPressureMeasurement objects found in the database
    public static function getAllMeasurements() {
        $allMeasurements = array();
        
        try {
            $db = Database::getDB();
            $stmt = $db->prepare(
                "select userName, bpID, systolicPressure, diastolicPressure,
                    dateAndTime, notes, userID
                from Users join BloodPressureMeasurements using (userID)");
            $stmt->execute();

            foreach ($stmt as $row) {
                $bp = new BloodPressureMeasurement($row);
                if (is_object($bp) && $bp->getErrorCount() == 0)
                    $allMeasurements[] = $bp;
            }
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        
        return $allMeasurements;
    }
    
    public static function getMeasurement($userName, $dateAndTime) {
        $measurement = null;
        try {
            $db = Database::getDB();
            $stmt = $db->prepare(
                "select userName, bpID, systolicPressure, diastolicPressure,
                    dateAndTime, notes, userID
                from Users join BloodPressureMeasurements using (userID)
                where userName = :userName and dateAndTime = :dateAndTime"
            );
            $stmt->execute(array(":userName" => $userName, ":dateAndTime" => $dateAndTime));
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row !== false)
                $measurement = new BloodPressureMeasurement($row);
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        
        return $measurement;
    }
    
    // returns an array of BloodPressureMeasurement objects, sorted by date
    public static function getMeasurementsBy($type, $value, $order = 'desc') {
        $allowedOrders = array('asc', 'desc');
        $allowedTypes = array('userName', 'userID');
        $measurements = array();
    
        try {
            if (!in_array($order, $allowedOrders))
                throw new Exception("$order is not an allowed order");
            if (!in_array($type, $allowedTypes))
                throw new PDOException("$type not allowed search criterion for blood pressure measurement");
            
            $db = Database::getDB();
            $stmt = $db->prepare(
                "select userName, bpID, systolicPressure, diastolicPressure,
                    dateAndTime, notes, userID
                from Users join BloodPressureMeasurements using (userID)
                where ($type = :$type)
                order by dateAndTime $order");
            $stmt->execute(array(":$type" => $value));
            
            foreach ($stmt as $row) {
                $bp = new BloodPressureMeasurement($row);
                if (is_object($bp) && $bp->getErrorCount() == 0)
                    $measurements[] = $bp;
            }
    
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    
        return $measurements;
    }
}
?>