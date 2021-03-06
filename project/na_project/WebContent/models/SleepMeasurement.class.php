<?php
class SleepMeasurement extends GenericModelObject implements JsonSerializable {
    
    const DEFAULT_UNITS = 'minutes';
    
    private $formInput;
    private $userName;
    private $datetime;
    private $notes;
    private $duration;
    private $units;
    
    public function __construct($formInput = null) {
        $this->formInput = $formInput;
        Messages::reset();
        $this->initialize();
    }
    
    public function getUserName() {
        return $this->userName;
    }
    
    public function getDateTime() {
        return $this->datetime;
    }
    
    public function getDate() {
        return is_object($this->datetime) ? $this->datetime->format("Y-m-d") : '';
    }
    
    public function getTime() {
        return is_object($this->datetime) ? $this->datetime->format("h:i a") : '';
    }
    
    public function getNotes() {
        return $this->notes;
    }

    public function getUnits() {
        return $this->units;
    }
    
    public function getMeasurement() {
        return $this->duration;
    }
    
    public function getParameters() {
        $params = array(
            "userName" => $this->userName,
            "dateAndTime" => $this->datetime->format("Y-m-d H:i"),
            "notes" => $this->notes,
            "duration" => $this->duration,
            "units" => $this->units
        );
    
        return $params;
    }
    
    public function __toString() {
        $dtVal = is_object($this->datetime) ? $this->datetime->format("Y-m-d h:i:s a") : '';
        $str =
            "User Name: [" . $this->userName . "]\n" .
            "Date and Time: [" . $dtVal . "]\n" .
            "Sleep Duration: [" . $this->duration . "]\n";
            "Notes: [" . $this->notes . "]\n" .
            "Units: [" . $this->units . "]";
        
        return $str;
    }
    
    protected function initialize() {
        $this->errors = array();
        $this->errorCount = 0;
        
        if (is_null($this->formInput)) {
            $this->userName = '';
            $this->datetime = new DateTime();
            $this->notes = '';
            $this->duration = '';
            $this->units = '';
        } else {
            $this->validateUserName();
            $this->validateDateAndTime();
            $this->validateNotes();
            $this->validateMeasurement();
            $this->validateUnits();
        }
    }
    
    private function validateUserName() {
        $this->userName = $this->extractForm($this->formInput, "userName");
        if (empty($this->userName)) {
            $this->setError("userName", "USER_NAME_EMPTY");
            return;
        }
    
        $options = array("options" => array("regexp" => "/^[a-zA-Z0-9_-]+$/"));
        if (!filter_var($this->userName, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("userName", "USER_NAME_HAS_INVALID_CHARS");
            return;
        }
        
        if (strlen($this->userName) > 15) {
            $this->setError("userName", "USER_NAME_TOO_LONG");
            return;
        }
    }
    
    private function validateDateAndTime() {
        // the date and time may be present as a single value or as separate values
        if (array_key_exists('dateAndTime', $this->formInput)) {
            $datetime = $this->extractForm($this->formInput, "dateAndTime");
            list($date, $time) = preg_split("/ /", $datetime);
        } else {
            $date = $this->extractForm($this->formInput, "date");
            $time = $this->extractForm($this->formInput, "time");
        }
        $this->datetime = new DateTime();
    
        if (empty($date)) {
            $this->setError("dateAndTime", "DATE_EMPTY");
            return;
        }
    
        if (empty($time)) {
            $this->setError("dateAndTime", "TIME_EMPTY");
            return;
        }
    
        $options = array("options" => array("regexp" => "/^((\d{4}[\/-]\d\d[\/-]\d\d)|(\d\d[\/-]\d\d[\/-]\d{4}))$/"));
        if (!filter_var($date, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("dateAndTime", "DATE_HAS_INVALID_CHARS");
            return;
        }
    
        $options = array("options" => array("regexp" => "/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?( ((am|pm)|(AM|PM)))?$/"));
        if (!filter_var($time, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("dateAndTime", "TIME_HAS_INVALID_CHARS");
            return;
        }
    
        try { $dt = new DateTime($date . ' ' . $time); }
        catch (Exception $e) {
            $this->setError("dateAndTime", "DATE_AND_TIME_INVALID");
            return;
        }
    
        $this->datetime = $dt;
    }
    
    private function validateNotes() {
        $this->notes = $this->extractForm($this->formInput, "notes");
    
        if (empty($this->notes))
            return;
    
        if (strlen($this->notes) > 255) {
            $this->setError("notes", "NOTES_ARE_TOO_LONG");
            return;
        }
    }
    
    private function validateMeasurement() {
        $this->duration = $this->extractForm($this->formInput, "duration");
        
        if (empty($this->duration)) {
            $this->setError("duration", "DURATION_EMPTY");
            return;
        }
        
        $options = array("options" => array("regexp" => "/^\d+$/"));
        if (!filter_var($this->duration, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("duration", "DURATION_HAS_INVALID_CHARS");
            return;
        }
        
        $this->duration = (int)$this->duration;
    }

    private function validateUnits() {
        $this->units = $this->extractForm($this->formInput, "units");
    
        if (empty($this->units)) {
            $this->units = self::DEFAULT_UNITS;
            return;
        }
    
        $allowed = array('minutes');
        if (!in_array($this->units, $allowed)) {
            $this->setError("units", "UNITS_INVALID");
            return;
        }
    }
    
    public function jsonSerialize() {
        $isoDateTime = $this->datetime->format('Y-m-d H:i');
        $isoDateTime[10] = 'T';
        $datetime_pieces = explode('T', $isoDateTime);
        $object = new stdClass();
    
        $object->duration = $this->duration;
        $object->dateAndTime = $isoDateTime;
        $object->date = $datetime_pieces[0];
        $object->time = $datetime_pieces[1];
        $object->notes = $this->notes;
        $object->userName = $this->userName;
        $object->units = $this->units;
        return $object;
    }

}
?>