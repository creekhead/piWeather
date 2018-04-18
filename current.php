<?php
include 'database.php';
if(isset($_GET['json'])){
    header('content-type: application/json; charset=utf-8');
    header("access-control-allow-origin: *");
}else{
   echo '<!DOCTYPE html>';
   echo '<head>';
   echo '<html>';
   echo '<meta charset="UTF-8">';
   echo '<title>PKNC Weather</title>';
   echo '<meta http-equiv="refresh" content="600">';

   echo '<link rel="stylesheet" href="css/weather-icons.css">';
   echo '<link rel="stylesheet" href="css/weather-icons-wind.css">';
   echo '<link rel="stylesheet" href="dashboard.css">';
   echo "<script src='http://code.jquery.com/jquery-2.2.4.min.js'></script>";
   echo "<script src='js/forecast.js'></script>";
   echo "<script src='js/moonphase.js'></script>";

   echo '</head>';
   echo '<body>';
}

date_default_timezone_set('America/New_York');

$con = new mysqli(DbSettings::$Address,DbSettings::$Username,DbSettings::$Password,DbSettings::$Schema);
//echo "PSWD:".DbSettings::$Password;
//echo "USER".DbSettings::$Username;
//exit;

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$result = $con->query('CALL GETJSON');
$fieldcount = mysqli_num_fields($result);
//print_r($result);
//print_r($fieldcount);
//exit;
//echo $result->num_rows;
//exit;
/*
while($row = mysqli_fetch_array($result)) { // Rows
echo "<BR>VAR: <b>$ row: </b><PRE>";
print_r($row);
print_r('<br><br/>Variable: row - /c/users/johnny/appdata/local/temp/rsub-4nov7q/current.php:25');
exit;
}
*/

if(!isset($_GET['debug'])){
    //echo "{ "; // Open document object
    //echo "\r\n\t\"WeatherObservations\" : {"; // Open weather observations object
}

if ($result->num_rows > 0) {
    $fields = array();
    while ($fieldinfo = mysqli_fetch_field($result)) {
        array_push($fields, $fieldinfo->name);
    }

    $numberOfRows = 0;
    while($row = mysqli_fetch_assoc($result)) { // Rows
	   $numberOfRows++;

    $parsed_wunder = json_decode(unserialize($row['wunder_json']));
    $parsed_open = json_decode(unserialize($row['openweather_json']));
    $parsed_forecast = json_decode(unserialize($row['openweather_forecast']));

    if(isset($_GET['raw'])){
        echo "<BR>VAR: <b>$ parsed_wunder: </b><PRE>";
        print_r($parsed_wunder);
        echo '<hr style="color:tomato">';
        print_r($parsed_open);
        echo '<hr style="color:tomato">';
        print_r($parsed_forecast);
        echo '<hr style="color:tomato">';


exit;
        exit;
    }


    //SUN PHASE
    $moon_phase = $parsed_wunder->moon_phase;
    $percentIlluminated = $moon_phase->percentIlluminated;
    $percentIlluminated_js=$percentIlluminated*.1;
    $ageOfMoon = $moon_phase->ageOfMoon;
    $phaseofMoon = $moon_phase->phaseofMoon;
    $hemisphere = $moon_phase->hemisphere;
    if(strlen(strstr(strtolower($phaseofMoon),'waxing'))>0) {
        $phaseofMoon_ww=1;   //true = waxing - shadow on the left
    }else{
        $phaseofMoon_ww=0;  //false = waning - shadow on the right
    }

    $moon_phase_string='PercentageIlluminated: '.$percentIlluminated.'% / '.$percentIlluminated_js;
    $moon_age_string='Age of Moon: '.$ageOfMoon;
    $phaseofMoon_string=$phaseofMoon.' '.$hemisphere.' H';

    $sun_phase = $parsed_wunder->sun_phase;
    $sunrise=$sun_phase->sunrise->hour.':'.$sun_phase->sunrise->minute;
    $sunrise = date("g:i a", strtotime($sunrise));
    $sunset=$sun_phase->sunset->hour.':'.$sun_phase->sunset->minute;
    $sunset=date("g:i a", strtotime($sunset));

    //LOC
    $station_id = $parsed_wunder->current_observation->station_id;
    $observation_time = $parsed_wunder->current_observation->observation_time;
    $local_epoch = $parsed_wunder->current_observation->local_epoch;
    $location = $parsed_wunder->current_observation->display_location->full;

    //TEMP
    $weather =$parsed_wunder->current_observation->weather;
    $temperature_string = $parsed_wunder->current_observation->temperature_string;
    $temp_f = $parsed_wunder->current_observation->temp_f;

    $temp_min = $parsed_open->main->temp_min;
    $temp_max = $parsed_open->main->temp_max;
    $clouds = $parsed_open->main->clouds;
    $id = $parsed_open->weather[0]->id;
    $main = $parsed_open->weather[0]->main;
    $description = $parsed_open->weather[0]->description;
    $icon = $parsed_open->weather[0]->icon;
/*
echo 'temp_min: <b>'.$temp_min.'</b><BR>';
echo 'temp_max: <b>'.$temp_max.'</b><BR>';
echo 'clouds: <b>'.$clouds.'</b><BR>';
echo 'id: <b>'.$id.'</b><BR>';
echo 'main: <b>'.$main.'</b><BR>';
echo 'description: <b>'.$description.'</b><BR>';
echo 'icon: <b>'.$icon.'</b><BR>';
echo '<img src="img/'.$icon.'.png">';
exit;
 */
    $relative_humidity = $parsed_wunder->current_observation->relative_humidity;
    $pressure_in = $parsed_wunder->current_observation->pressure_in;
    $pressure_mb = $parsed_wunder->current_observation->pressure_mb;
    $dewpoint_string = $parsed_wunder->current_observation->dewpoint_string;
    $dewpoint_f = $parsed_wunder->current_observation->dewpoint_f;
    $feelslike_string = $parsed_wunder->current_observation->feelslike_string;
    $feelslike_f = $parsed_wunder->current_observation->feelslike_f;
    $visibility_mi = $parsed_wunder->current_observation->visibility_mi;
    $solarradiation = $parsed_wunder->current_observation->solarradiation;
    $UV = $parsed_wunder->current_observation->UV;



    //WIND
    $wind_string = $parsed_wunder->current_observation->wind_string;
    $wind_dir = $parsed_wunder->current_observation->wind_dir;
    $wind_degrees = $parsed_wunder->current_observation->wind_degrees;
    $wind_mph = $parsed_wunder->current_observation->wind_mph;
    $wind_gust_mph = $parsed_wunder->current_observation->wind_gust_mph;

    //RAIN
    $precip_1hr_string = $parsed_wunder->current_observation->precip_1hr_string;
    $precip_1hr_in = $parsed_wunder->current_observation->precip_1hr_in;
    $precip_today_string = $parsed_wunder->current_observation->precip_today_string;
    $precip_today_in = $parsed_wunder->current_observation->precip_today_in;
    $leaf_wetness = $parsed_wunder->current_observation->leaf_wetness;

    $icon = $parsed_wunder->current_observation->icon;
    $icon_url = $parsed_wunder->current_observation->icon_url;
    $forecast_url = $parsed_wunder->current_observation->forecast_url;
    $history_url = $parsed_wunder->current_observation->history_url;
    $ob_url = $parsed_wunder->current_observation->ob_url;

    echo '<div class="weatherCurrent">';
        echo '<div class="weatherTemp">';
            echo $temp_f.'<span class="degSmall">&deg;F</span>';
            echo '<span class="weather_minmax" min="'.$temp_min.'" max="'.$temp_max.'">'.$temp_min.'&deg;F/'.$temp_max.'&deg;F</span>';
            //echo '<div class="feelsLike">'.$feelslike_string.'</div>';
        echo '</div>';




        echo '<div class="weatherCont1">';

            echo '<img src="'.$icon_url.'" style="width: 50px;height: 50px;">';

            echo '<div class="currentTempDesc">';
                echo ucwords($parsed_open->weather[0]->description);
                echo '<img class="currentTempImage" src="img/'.$parsed_open->weather[0]->icon.'.png">';
            echo '</div>';

            echo '<div class="weatherSunPhase">';
                echo '<i class="wi wi-sunrise"></i>';
                    echo '<em>SUNRISE: <b>'.$sunrise.'</b></em>';
                    //echo '<br>';
                echo '<i class="wi wi-sunset"></i>';
                    echo '<em>SUNSET: <b>'.$sunset.'</b></em>';
            echo '</div>';//weatherSunPhase

        echo '</div>';//weatherCont1

//baromoter
        echo '<div class="weatherBaromoter">';
            echo '<canvas id="barometr" pressure="'.$pressure_mb.'" width="96" height="96">800 hPa</canvas>';
            echo '<img src="img/face.png" id="face" class="bar"/>';
            echo '<img src="img/cloudy.png" id="cloudy" class="bar"/>';
            echo '<img src="img/sun.png" id="sun" class="bar"/>';
            echo '<img src="img/rain.png" id="rain" class="bar"/>';
            echo '<img src="img/hand.png" id="hand" class="bar"/>';
            echo '<img src="img/shadow.png" id="shadow" class="bar"/>';
            echo '<div class="mbPressure">'.$pressure_mb.'</div>';

            //MOONPHASE
            echo '<div id="moonPhaseContainer">';
                echo '<div id="moonPhase" percent="'.$percentIlluminated.'" phase="'.$phaseofMoon_ww.'" class="moonPhase" style=""></div>';
                echo '<em>'.$moon_phase_string.'</em>';
                echo '<em>'.$phaseofMoon_string.'</em>';
                echo '<em>'.$moon_age_string.'</em>';
            echo '</div>';//moonPhaseContainer
        echo '</div>';//weatherBaromoter



        echo '<div class="weatherUpdateDate">';
            echo date('D M j G:i:s A',$local_epoch);
        echo '</div>';
    echo '</div>';//weatherCurrent


    if(isset($_GET['forecast'])){
        include('forecast.php');
    }//end forecast

    echo '</body>';
    echo '</html>';
exit;
if(isset($_GET['debug'])){

    //WEATHER
    echo "<br><br>WEATHER:<br>";
    echo 'station_id: <b>'.$station_id.'</b><BR>';
    echo 'observation_time: <b>'.$observation_time.'</b><BR>';
    echo 'local_epoch: <b>'.$local_epoch.'</b><BR>';
    echo 'local_epoch: <b>'.date('m-j-y h:i:s A',$local_epoch).'</b><BR>';
    echo 'location: <b>'.$location.'</b><BR>';
    echo 'weather: <b>'.$weather.'</b><BR>';
    echo 'temperature_string: <b>'.$temperature_string.'</b><BR>';
    echo 'temp_f: <b>'.$temp_f.'</b><BR>';
    echo 'pressure_in: <b>'.$pressure_in.'</b><BR>';
    echo 'pressure_mb: <b>'.$pressure_mb.'</b><BR>';
    echo 'dewpoint_string: <b>'.$dewpoint_string.'</b><BR>';
    echo 'dewpoint_f: <b>'.$dewpoint_f.'</b><BR>';
    echo 'feelslike_string: <b>'.$feelslike_string.'</b><BR>';
    echo 'feelslike_f: <b>'.$feelslike_f.'</b><BR>';
    echo 'visibility_mi: <b>'.$visibility_mi.'</b><BR>';
    echo 'solarradiation: <b>'.$solarradiation.'</b><BR>';
    echo 'UV: <b>'.$UV.'</b><BR>';

    //WIND
    echo "<br><br>WIND:<br>";
    echo 'wind_string: <b>'.$wind_string.'</b><BR>';
    echo 'wind_dir: <b>'.$wind_dir.'</b><BR>';
    echo 'wind_degrees: <b>'.$wind_degrees.'</b><BR>';
    echo 'wind_mph: <b>'.$wind_mph.'</b><BR>';
    echo 'wind_gust_mph: <b>'.$wind_gust_mph.'</b><BR>';

    //RAIN
    echo "<br><br>RAIN:<br>";
    echo 'precip_1hr_string: <b>'.$precip_1hr_string.'</b><BR>';
    echo 'precip_1hr_in: <b>'.$precip_1hr_in.'</b><BR>';
    echo 'precip_today_string: <b>'.$precip_today_string.'</b><BR>';
    echo 'precip_today_in: <b>'.$precip_today_in.'</b><BR>';
    echo 'leaf_wetness: <b>'.$leaf_wetness.'</b><BR>';

    //MISC
    echo "<br><br>MISC:<br>";
    echo 'icon: <b>'.$icon.'</b><BR>';
    echo 'icon_url: <b>'.$icon_url.'</b><img src="'.$icon_url.'"><BR>';
    echo 'forecast_url: <b>'.$forecast_url.'</b><a href="'.$forecast_url.'">link</a><BR>';
    echo 'history_url: <b>'.$history_url.'</b><a href="'.$history_url.'">link</a><BR>';
    echo 'ob_url: <b>'.$ob_url.'</b><a href="'.$ob_url.'">link</a><BR>';



    echo "Current temperature in ${location} is: ${temp_f}\n degrees and it is currently ${weather}";

}

    //LOC
    $w_row['station_id'] = $parsed_wunder->current_observation->station_id;
    $w_row['observation_time'] = $parsed_wunder->current_observation->observation_time;
    $w_row['local_epoch'] = $parsed_wunder->current_observation->local_epoch;
    $w_row['location'] = $parsed_wunder->current_observation->display_location->full;

    $w_row['weather'] =$parsed_wunder->current_observation->weather;
    $w_row['temperature_string'] = $parsed_wunder->current_observation->temperature_string;
    $w_row['temp_f'] = $parsed_wunder->current_observation->temp_f;
    $w_row['relative_humidity'] = $parsed_wunder->current_observation->relative_humidity;
    $w_row['pressure_in'] = $parsed_wunder->current_observation->pressure_in;
    $w_row['pressure_mb'] = $parsed_wunder->current_observation->pressure_mb;
    $w_row['dewpoint_string'] = $parsed_wunder->current_observation->dewpoint_string;
    $w_row['dewpoint_f'] = $parsed_wunder->current_observation->dewpoint_f;
    $w_row['feelslike_string'] = $parsed_wunder->current_observation->feelslike_string;
    $w_row['feelslike_f'] = $parsed_wunder->current_observation->feelslike_f;
    $w_row['visibility_mi'] = $parsed_wunder->current_observation->visibility_mi;
    $w_row['solarradiation'] = $parsed_wunder->current_observation->solarradiation;
    $w_row['UV'] = $parsed_wunder->current_observation->UV;

    //WIND
    $w_row['wind_string'] = $parsed_wunder->current_observation->wind_string;
    $w_row['wind_dir'] = $parsed_wunder->current_observation->wind_dir;
    $w_row['wind_degrees'] = $parsed_wunder->current_observation->wind_degrees;
    $w_row['wind_mph'] = $parsed_wunder->current_observation->wind_mph;
    $w_row['wind_gust_mph'] = $parsed_wunder->current_observation->wind_gust_mph;

    //RAIN
    $w_row['precip_1hr_string'] = $parsed_wunder->current_observation->precip_1hr_string;
    $w_row['precip_1hr_in'] = $parsed_wunder->current_observation->precip_1hr_in;
    $w_row['precip_today_string'] = $parsed_wunder->current_observation->precip_today_string;
    $w_row['precip_today_in'] = $parsed_wunder->current_observation->precip_today_in;
    $w_row['leaf_wetness'] = $parsed_wunder->current_observation->leaf_wetness;

    $w_row['icon'] = $parsed_wunder->current_observation->icon;
    $w_row['icon_url'] = $parsed_wunder->current_observation->icon_url;
    $w_row['forecast_url'] = $parsed_wunder->current_observation->forecast_url;
    $w_row['history_url'] = $parsed_wunder->current_observation->history_url;
    $w_row['ob_url'] = $parsed_wunder->current_observation->ob_url;



    $w_row["AMBIENT_TEMPERATURE"]=$temp_f;
    $w_row["HUMIDITY"]=$relative_humidity;
    $w_row["WIND_SPEED"]=$wind_mph;
    $w_row["FEELS_LIKE"]=$feelslike_f;
    $w_row["AMBIENT_TEMPERATURE_STRING"]=$temperature_string;
    $w_row["DEWPOINT_STRING"]=$dewpoint_string;
    $w_row["RAINFALL_STRING"]=$precip_today_string;
    $w_row["HUMIDITY_STRING"]=$relative_humidity;
    $w_row["WIND_SPEED"]=$wind_mph;
    $w_row["WIND_DIRECTION"]=$wind_dir;
    $w_row["AIR_PRESSURE"]=$pressure_in;

echo json_encode($w_row);
exit;

        if ($numberOfRows > 1) {
            echo ", ";
        }

        echo "\r\n\t\t\"Observation" . $numberOfRows . "\" : {";

        echo "\r\n\t\t\t\"FEELS_LIKE\" : " . "\"" . $FEELS_LIKE . "° F\",";
        echo "\r\n\t\t\t\"AMBIENT_TEMPERATURE_STRING\" : " . "\"" . $temp_f . "° F\",";
        echo "\r\n\t\t\t\"DEWPOINT_STRING\" : " . "\"" . $dewpoint_f . "° F\",";
        echo "\r\n\t\t\t\"RAINFALL_STRING\" : " . "\"" . $precip_1hr_in . " in\",";
        echo "\r\n\t\t\t\"HUMIDITY_STRING\" : " . "\"" . $relative_humidity . " in\",";
        echo "\r\n\t\t\t\"WIND_SPEED\" : " . "\"" . $wind_mph . " in\",";
        echo "\r\n\t\t\t\"WIND_DIRECTION\" : " . "\"" . $wind_dir . " in\",";
        echo "\r\n\t\t\t\"AIR_PRESSURE\" : " . "\"" . $pressure_in . " in\",";

        foreach ($w_row as $key => $value) {
            echo "\r\n\t\t\t\"" . $key . "\" : " . "\"" . $value . "\"";
            echo ",";
        }

        echo "\r\n\t\t}";
    }

    $result->close();
    $con->next_result();
}
echo "\r\n\t}"; // Close weather observations object
/*

$result = $con->query('call GETDAILYRECORDS');
$fieldcount = mysqli_num_fields($result);
echo ",\r\n\t\"DailyStats\" : {"; // Open daily stats object

if ($result->num_rows > 0) {
    $fields = array();
    while ($fieldinfo = mysqli_fetch_field($result)) {
        array_push($fields, $fieldinfo->name);
    }

    $row = mysqli_fetch_array($result); // Only one row of data
    for ($i = 0; $i < $fieldcount; $i++) {   // Columns
        $fieldInfo = mysqli_fetch_field($result);
        $fieldName = $fields[$i];
        $fieldValue = $row[$i];

        if (($fieldName == "LowSinceMidnight") || ($fieldName == "HighSinceMidnight")) {
            if (Settings::$showMetricAndCelsiusMeasurements == "1") {
                echo "\r\n\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° C\",";
            }
            else {
                $fieldValue = convertCelsiusToFahrenheit($fieldValue);
                echo "\r\n\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° F\",";
            }
        }

        echo "\r\n\t\t\"" . $fieldName . "\" : " . "\"" . $fieldValue . "\"";

        if ($i+1 < $fieldcount) {
            echo ",";
        }
    }
}
echo "\r\n\t}"; // Close daily stats object

*/

echo ",\r\n\t\"Settings\" : {"; // Open settings object
echo "\r\n\t\t\"showMetricAndCelsiusMeasurements\" : " . "\"" . Settings::$showMetricAndCelsiusMeasurements . "\",";
echo "\r\n\t\t\"showPressureInMillibars\" : " . "\"" . Settings::$showPressureInMillibars . "\"";

echo "\r\n\t}"; // Close settings object

echo "\r\n}"; // Close document object

//print_r($result);
//exit;

$result->close();
mysqli_close($con);

//
//$result->close();
//mysqli_close($con);

?>
