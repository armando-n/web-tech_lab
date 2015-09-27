<?php
class BloodPressureMeasurement {
    
    private $formInput;
    private $errors;
    private $errorCount;
    private $date;
    private $time;
    private $bloodPressure;
    
    public function __construct($formInput = null) {
        $this->formInput = $formInput;
        Messages::reset();
        $this->initialize();
    }
    
    public function getError($errorName) {
        if (isset($this->errors[$errorName]))
            return $this->errors[$errorName];
    
        return "";
    }
    
    public function setError($errorName, $errorValue) {
        $this->errors[$errorName] =  Messages::getError($errorValue);
        $this->errorCount++;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getErrorCount() {
        return $this->errorCount;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    public function getTime() {
        return $this->time;
    }
    
    public function getMeasurement() {
        return $this->bloodPressure;
    }
    
    public function __toString() {
        $str =
            "Date: [" . $this->date . "]\n" .
            "Time: [" . $this->time . "]\n" .
            "Blood Pressure: [" . $this->bloodPressure . "]";
        
        return $str;
    }
    
    private function initialize() {
        $this->errors = array();
        $this->errorCount = 0;
        
        if (is_null($this->formInput)) {
            $this->date = '';
            $this->time = '';
            $this->bloodPressure = '';
        } else {
            $this->validateDate();
            $this->validateTime();
            $this->validateMeasurement();
        }
    }
    
    private function validateDate() {
    
    }
    
    private function validateTime() {
    
    }
    
    private function validateMeasurement() {
    
    }

}
?>