<?php
include "database.php";

getRawWeatherData_tosql();

exit;

//DEBUG
$parsed_json = json_decode($json_string);
$location = $parsed_json->location->city;
$weather =$parsed_json->current_observation->weather;
$temp_c = $parsed_json->current_observation->temp_c;
echo "Current temperature in ${location} is: ${temp_c}\n degrees and it is currently ${weather}";



function getRawWeatherData_tosql(){
	date_default_timezone_set('America/New_York');
	$con = new mysqli(DbSettings::$Address,DbSettings::$Username,DbSettings::$Password,DbSettings::$Schema);

	//garbage collection
	$hour=date('H');
	$minutes=date('m');
 
	//between 11:30 and midnight truncate table
	if($hour==23 && ($minutes>30 && $minutes<59)){
		$result = $con->query("SELECT COUNT(*) as count FROM wunder_json");
		$current_row_count = $result->fetch_object()->count;

		if($current_row_count>20){
			$sql="TRUNCATE TABLE wunder_json";
			if ($con->query($sql) === TRUE) {
		   		echo "Table truncated!<br>";
			}
		}
  	}

	$json_wunderground = file_get_contents("http://api.wunderground.com/api/a4b1e907bb43a8dc/conditions/astronomy/q/CT/Newington.json");

	$json_wunderground_all = file_get_contents("http://api.wunderground.com/api/a4b1e907bb43a8dc/conditions/astronomy/hourly/forecast/forecast10day/almanac/alerts/currenthurricane/satellite/q/CT/Newington.json");

	$json_openweather = file_get_contents("http://api.openweathermap.org/data/2.5/weather?lat=41.68593&lon=-72.71117&appid=5c507080d1448544b56d307f400bdeee&mode=json&units=imperial&type=accurate");

	$json_openweather_forecast = file_get_contents("http://api.openweathermap.org/data/2.5/forecast?lat=41.68593&lon=-72.71117&appid=5c507080d1448544b56d307f400bdeee&mode=json&units=imperial&type=accurate");
 
	$json_wunderground_serialized=serialize($json_wunderground);
	$json_wunderground_serialized_all=$json_wunderground_all;
	$json_openweather_serialized=serialize($json_openweather);
	$json_openweather_forecast_serialized=serialize($json_openweather_forecast);


	if (mysqli_connect_errno()) {
	    die('Error conecting to DB');
	}else{
		$sql = "INSERT INTO wunder_json (wunder_json,wunder_json_all,openweather_json,openweather_forecast) VALUES ('".$json_wunderground_serialized."','".$json_wunderground_serialized_all."','".$json_openweather_serialized."','".$json_openweather_forecast_serialized."')";

		if ($con->query($sql) === TRUE) {
	   		echo "New record created successfully";
		} 
	}




	$con->close();
}




