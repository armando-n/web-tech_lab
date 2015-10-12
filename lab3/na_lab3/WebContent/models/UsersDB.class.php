<?php
class UsersDB {
    
    // adds the specified User object to the database
    public static function addUser($user, $dbName = null, $configFile = null) {
        $returnUserID = -1;
        
        if (!($user instanceof User))
            throw new InvalidArgumentException("Error: Not valid User object");
        
        try {
            $db = Database::getDB($dbName, $configFile);
            $stmt = $db->prepare(
                "insert into Users (userName, password)
                    values (:userName, :password)");
            $stmt->execute(array(
                ":userName" => $user->getUserName(),
                ":password" => $user->getPassword()
            ));
            $returnUserID = $db->lastInsertId("userID");
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        
        return $returnUserID;
    }
    
    // returns an array of User objects for all users in the database
    public static function getAllUsers($dbName = null, $configFile = null) {
        $allUsers = array();
        
        try {
            $db = Database::getDB($dbName, $configFile);
            $stmt = $db->prepare("select * from Users");
            $stmt->execute();
            
            foreach ($stmt as $row) {
                $user = new User($row);
                if (!is_object($user) || !empty($user->getErrors()))
                    throw new PDOException("Failed to create valid user");
                
                $allUsers[] = $user;
            }
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        
        return $allUsers;
    }
    
    // returns a User object whose $type field has value $value
    public static function getUserBy($type, $value, $dbName = null, $configFile = null) {
        $allowed = array('userID', 'userName', 'dateCreated');
        $user = null;
        
        try {
            if (!in_array($type, $allowed))
                throw new PDOException("$type not allowed search criterion for User");
            
            $db = Database::getDB($dbName, $configFile);
            $stmt = $db->prepare("select * from Users where ($type = :$type)");
            $stmt->execute(array(":$type" => $value));
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row !== false)
                $user = new User($row);
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        
        return $user;
    }
    
    public static function getAllUsersSortedByDateCreated($order, $dbName = null, $configFile = null) {
        $allowedOrders = array('asc', 'desc');
        $allUsers = array();
        
        try {
            if (!in_array($order, $allowedOrders))
                throw new Exception("$order is not an allowed order");
            
            $db = Database::getDB($dbName, $configFile);
            $stmt = $db->prepare("select * from Users order by dateCreated $order");
            $stmt->execute();
        
            foreach ($stmt as $row) {
                $user = new User($row);
                if (!is_object($user) || !empty($user->getErrors()))
                    throw new PDOException("Failed to create valid user");
        
                $allUsers[] = $user;
            }
        
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        
        return $allUsers;
    }
    
    // returns an array of User objects created since the specified date string
    public static function getUsersCreatedSince($dateString, $dbName = null, $configFile = null) {
        return UsersDB::getUsersByDate($dateString, 'after', $dbName, $configFile);
    }
    
    // returns an array of User objects created by the specified date string
    public static function getUsersCreatedBy($dateString, $dbName = null, $configFile = null) {
        return UsersDB::getUsersByDate($dateString, 'before', $dbName, $configFile);
    }
    
    private static function getUsersByDate($dateString, $direction, $dbName = null, $configFile = null) {
        $allowedDirections = array('before', 'after');
        $users = array();
    
        try {
            if (!in_array($direction, $allowedDirections))
                throw new PDOException("$direction is not an allowed direction");
            $operator = ($direction === 'before') ? '<=' : '>=';
            
            $datetime = new DateTime($dateString);
            $db = Database::getDB($dbName, $configFile);
            $stmt = $db->prepare("select * from Users where dateCreated $operator :date");
            $stmt->execute(array(":date" => $datetime->format('Y-m-d')));
    
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rows !== false)
                foreach ($rows as $row)
                    $users[] = new User($row);
    
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            if (stristr($e->getMessage(), 'Failed to parse') !== false)
                throw new Exception("Invalid date: $dateString");
            else
                throw $e;
        }
    
        return $users;
    }
    
    public static function addUserTest($user) {
        return UsersDB::addUser($user, 'dhma_testDB', 'myConfig.ini');
    }
    
    public static function getAllUsersTest() {
        return UsersDB::getAllUsers('dhma_testDB', 'myConfig.ini');
    }
    
    public static function getUserByTest($type, $value) {
        return UsersDB::getUserBy($type, $value, 'dhma_testDB', 'myConfig.ini');
    }
    
    public static function getUsersCreatedSinceTest($dateString) {
        return UsersDB::getUsersCreatedSince($dateString, 'dhma_testDB', 'myConfig.ini');
    }
    
    public static function getUsersCreatedByTest($dateString) {
        return UsersDB::getUsersCreatedBy($dateString, 'dhma_testDB', 'myConfig.ini');
    }
    
    public static function getAllUsersSortedByDateCreatedTest($order) {
        return UsersDB::getAllUsersSortedByDateCreated($order, 'dhma_testDB', 'myConfig.ini');
    }
}
?>