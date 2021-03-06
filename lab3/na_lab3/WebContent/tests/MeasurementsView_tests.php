<?php
include_once("../views/MeasurementsView.class.php");
include_once("../models/GenericModelObject.class.php");
include_once("../models/User.class.php");
include_once("../models/UserProfile.class.php");
include_once("../models/GlucoseMeasurement.class.php");
include_once("../models/BloodPressureMeasurement.class.php");
include_once("../models/CalorieMeasurement.class.php");
include_once("../models/ExerciseMeasurement.class.php");
include_once("../models/SleepMeasurement.class.php");
include_once("../models/WeightMeasurement.class.php");
include_once("../models/Messages.class.php");
?><!DOCTYPE html>
<html>
<head>
    <title>Basic tests for MeasurementsView</title>
    <meta charset="utf-8" />
    <meta name="author" content="Armando Navarro" />
</head>
<body>

<h1>MeasurementsView test</h1>

<h2>It should call show and display the Past Measurements view</h2>
<?php
$measurements = initData();
MeasurementsView::showBody($measurements);
?>

<h2>It should call show and display an error message when null input is provided</h2>
<?php MeasurementsView::showBody(null); ?>

<h2>It should call show and display a message for each measurement type that has nothing to show when empty input is provided</h2>
<?php MeasurementsView::showBody(array('glucose' => '')); ?>

<h2>It should call show and display a message for each type that has nothing to show when a null value is provided</h2>
<?php MeasurementsView::showBody(array('glucose' => null)); ?>
    
</body>
</html>
<?php
function initData() {
    $glucoseMeasurements = array();
    $bloodPressureMeasurements = array();
    $calorieMeasurements = array();
    $exerciseMeasurements = array();
    $sleepMeasurements = array();
    $weightMeasurements = array();

    $userInput = array(
            "userName" => "armando-n",
            "password1" => "password123",
            "password2" => "password123"
    );
    $user = new User($userInput);
    $uData = new UserProfile(null);

    $glucoseInput1 = array(
            "userName" => "armando-n",
            "date" => "2015-09-27",
            "time" => "8:15",
            "glucose" => "95"
    );
    $glucoseInput2 = array(
            "userName" => "armando-n",
            "date" => "2015-09-26",
            "time" => "8:22",
            "glucose" => "120"
    );
    $glucoseInput3 = array(
            "userName" => "armando-n",
            "date" => "2015-09-25",
            "time" => "8:15",
            "glucose" => "110"
    );
    $glucoseInput4 = array(
            "userName" => "armando-n",
            "date" => "2015-09-24",
            "time" => "8:12",
            "glucose" => "112"
    );
    $glucoseMeasurements[] = new GlucoseMeasurement($glucoseInput1);
    $glucoseMeasurements[] = new GlucoseMeasurement($glucoseInput2);
    $glucoseMeasurements[] = new GlucoseMeasurement($glucoseInput3);
    $glucoseMeasurements[] = new GlucoseMeasurement($glucoseInput4);

    $bloodPressureInput1 = array(
            "userName" => "armando-n",
            "date" => "2015-09-27",
            "time" => "14:00",
            "systolicPressure" => "120",
            "diastolicPressure" => "80"
    );
    $bloodPressureInput2 = array(
            "userName" => "armando-n",
            "date" => "2015-09-26",
            "time" => "14:05",
            "systolicPressure" => "110",
            "diastolicPressure" => "90"
    );
    $bloodPressureInput3 = array(
            "userName" => "armando-n",
            "date" => "2015-09-25",
            "time" => "14:02",
            "systolicPressure" => "115",
            "diastolicPressure" => "95"
    );
    $bloodPressureInput4 = array(
            "userName" => "armando-n",
            "date" => "2015-09-24",
            "time" => "14:00",
            "systolicPressure" => "125",
            "diastolicPressure" => "78"
    );
    $bloodPressureMeasurements[] = new BloodPressureMeasurement($bloodPressureInput1);
    $bloodPressureMeasurements[] = new BloodPressureMeasurement($bloodPressureInput2);
    $bloodPressureMeasurements[] = new BloodPressureMeasurement($bloodPressureInput3);
    $bloodPressureMeasurements[] = new BloodPressureMeasurement($bloodPressureInput4);

    $calorieInput1 = array(
            "userName" => "armando-n",
            "date" => "2015-09-27",
            "time" => "21:00",
            "calories" => "1800"
    );
    $calorieInput2 = array(
            "userName" => "armando-n",
            "date" => "2015-09-26",
            "time" => "21:03",
            "calories" => "1540"
    );
    $calorieInput3 = array(
            "userName" => "armando-n",
            "date" => "2015-09-25",
            "time" => "21:01",
            "calories" => "1620"
    );
    $calorieInput4 = array(
            "userName" => "armando-n",
            "date" => "2015-09-24",
            "time" => "21:00",
            "calories" => "1460"
    );
    $calorieMeasurements[] = new CalorieMeasurement($calorieInput1);
    $calorieMeasurements[] = new CalorieMeasurement($calorieInput2);
    $calorieMeasurements[] = new CalorieMeasurement($calorieInput3);
    $calorieMeasurements[] = new CalorieMeasurement($calorieInput4);

    $exerciseInput1 = array(
            "userName" => "armando-n",
            "date" => "2015-09-27",
            "time" => "20:00",
            "type" => "running",
            "duration" => "60"
    );
    $exerciseInput2 = array(
            "userName" => "armando-n",
            "date" => "2015-09-26",
            "time" => "20:02",
            "type" => "running",
            "duration" => "56"
    );
    $exerciseInput3 = array(
            "userName" => "armando-n",
            "date" => "2015-09-25",
            "time" => "20:05",
            "type" => "running",
            "duration" => "40"
    );
    $exerciseInput4 = array(
            "userName" => "armando-n",
            "date" => "2015-09-24",
            "time" => "20:00",
            "type" => "running",
            "duration" => "58"
    );
    $exerciseMeasurements[] = new ExerciseMeasurement($exerciseInput1);
    $exerciseMeasurements[] = new ExerciseMeasurement($exerciseInput2);
    $exerciseMeasurements[] = new ExerciseMeasurement($exerciseInput3);
    $exerciseMeasurements[] = new ExerciseMeasurement($exerciseInput4);

    $sleepInput1 = array(
            "userName" => "armando-n",
            "date" => "2015-09-27",
            "time" => "22:00",
            "duration" => "480"
    );
    $sleepInput2 = array(
            "userName" => "armando-n",
            "date" => "2015-09-26",
            "time" => "22:12",
            "duration" => "460"
    );
    $sleepInput3 = array(
            "userName" => "armando-n",
            "date" => "2015-09-25",
            "time" => "22:35",
            "duration" => "464"
    );
    $sleepInput4 = array(
            "userName" => "armando-n",
            "date" => "2015-09-24",
            "time" => "22:02",
            "duration" => "472"
    );
    $sleepMeasurements[] = new SleepMeasurement($sleepInput1);
    $sleepMeasurements[] = new SleepMeasurement($sleepInput2);
    $sleepMeasurements[] = new SleepMeasurement($sleepInput3);
    $sleepMeasurements[] = new SleepMeasurement($sleepInput4);

    $weightInput1 = array(
            "userName" => "armando-n",
            "date" => "2015-09-27",
            "time" => "20:45",
            "weight" => "140.5"
    );
    $weightInput2 = array(
            "userName" => "armando-n",
            "date" => "2015-09-26",
            "time" => "20:50",
            "weight" => "139.5"
    );
    $weightInput3 = array(
            "userName" => "armando-n",
            "date" => "2015-09-25",
            "time" => "20:28",
            "weight" => "140"
    );
    $weightInput4 = array(
            "userName" => "armando-n",
            "date" => "2015-09-24",
            "time" => "20:46",
            "weight" => "141"
    );
    $weightMeasurements[] = new WeightMeasurement($weightInput1);
    $weightMeasurements[] = new WeightMeasurement($weightInput2);
    $weightMeasurements[] = new WeightMeasurement($weightInput3);
    $weightMeasurements[] = new WeightMeasurement($weightInput4);

    $measurementData = array();
    $measurementData["glucose"] = $glucoseMeasurements;
    $measurementData["bloodPressure"] = $bloodPressureMeasurements;
    $measurementData["calories"] = $calorieMeasurements;
    $measurementData["exercise"] = $exerciseMeasurements;
    $measurementData["sleep"] = $sleepMeasurements;
    $measurementData["weight"] = $weightMeasurements;
    
    return $measurementData;
}
?>