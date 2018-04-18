<?php
include "database.php";

getRawWeatherData_tosql();exit;

getRawWeatherData_fromsql();

exit;

$parsed_json = json_decode($json_string);
$location = $parsed_json->location->city;
$weather =$parsed_json->current_observation->weather;
$temp_c = $parsed_json->current_observation->temp_c;
echo "Current temperature in ${location} is: ${temp_c}\n degrees and it is currently ${weather}";



function getRawWeatherData_fromsql(){

	$con = new mysqli(DbSettings::$Address,DbSettings::$Username,DbSettings::$Password,DbSettings::$Schema);

	if (mysqli_connect_errno()) {
	    die('Error conecting to DB');
	}else{
		$sql = "SELECT * FROM wunder_json
				ORDER BY TIMESTAMP DESC
				LIMIT 1;";

		$result = $con->query($sql);

		if ($result->num_rows > 0) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		    	$wunder_json=unserialize($row["wunder_json"]);
		    	$openweather_json=unserialize($row["openweather_json"]);

		    	$parsed_json = json_decode($wunder_json);
		    	echo "<BR>VAR: <b>$ parsed_json: </b><PRE>";
		    	print_r($parsed_json);
		    	print_r('<br><br/>Variable: parsed_json - /c/users/johnny/appdata/local/temp/rsub-wah9iq/getwunderground_data.php:38');
		    	exit;
				$location = $parsed_json->location->city;
				$weather =$parsed_json->current_observation->weather;
				$temp_c = $parsed_json->current_observation->temp_c;
				echo "Current temperature in ${location} is: ${temp_c}\n degrees and it is currently ${weather}";

		    	echo "<PRE>";
		    	print_r($wunder_json);
		    	echo '<hr style="color:tomato">';
		    	print_r($openweather_json);
		    	exit;
		        echo "wunder_json: " . $row["wunder_json"]. " - openweather_json: " . $row["openweather_json"]. "<br>";
		    }
		} else {
		    echo "0 results";
		}
	}

	$con->close();
}

function getRawWeatherData_tosql(){
	//$json_wunderground = file_get_contents("http://api.wunderground.com/api/a4b1e907bb43a8dc/conditions/q/CT/Newington.json");
	$json_wunderground = file_get_contents("http://api.wunderground.com/api/a4b1e907bb43a8dc/conditions/astronomy/q/CT/Newington.json");
	$json_openweather = file_get_contents("http://api.openweathermap.org/data/2.5/weather?zip=06111,us&appid=5c507080d1448544b56d307f400bdeee&mode=json&units=imperial");

	$json_openweather_forecast = file_get_contents("http://api.openweathermap.org/data/2.5/forecast?zip=06111,us&appid=5c507080d1448544b56d307f400bdeee&mode=json&units=imperial");

	//$json_wunderground='testdata';
	//$json_openweather='testdata';
	$json_wunderground_serialized=serialize($json_wunderground);
	$json_openweather_serialized=serialize($json_openweather);
	$json_openweather_forecast_serialized=serialize($json_openweather_forecast);
	$con = new mysqli(DbSettings::$Address,DbSettings::$Username,DbSettings::$Password,DbSettings::$Schema);

	if (mysqli_connect_errno()) {
	    die('Error conecting to DB');
	}else{
		$sql = "INSERT INTO wunder_json (wunder_json,openweather_json,openweather_forecast) VALUES ('".$json_wunderground_serialized."','".$json_openweather_serialized."','".$json_openweather_forecast_serialized."')";

		if ($con->query($sql) === TRUE) {
	   		echo "New record created successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $con->error;
		}
	}

	$con->close();
}




