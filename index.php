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
   echo '<meta name="apple-mobile-web-app-capable" content="yes">';
   echo '<meta name="apple-mobile-web-app-status-bar-style" content="black">';
    echo "<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />";

    /********************************************************
    *                       CSS                   *
    ********************************************************/
   echo '<link rel="stylesheet" href="css/weather-icons.css">';
   echo '<link rel="stylesheet" href="css/weather-icons-wind.css">';
   echo '<link rel="stylesheet" href="css/dashboard.css">';
   //echo '<link rel="stylesheet" href="css/dashboard.js">';

    /********************************************************
    *                       JAVASCRIPT                   *
    ********************************************************/
   echo "<script src='http://code.jquery.com/jquery-2.2.4.min.js'></script>";
   echo "<script src='js/forecast.js'></script>";
   echo "<script src='js/moonphase.js'></script>";
   echo "<script src='js/compass.js'></script>";
   echo "<script src='js/dashboard.js'></script>";
   //echo "<script src='js/dashboard_google.js'></script>";    //uses google charts

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
        $parsed_wunder_all = json_decode($row['wunder_json_all']);
        $parsed_open = json_decode(unserialize($row['openweather_json']));
        $parsed_forecast = json_decode(unserialize($row['openweather_forecast']));

        if(isset($_GET['raw'])){
            echo "<BR>VAR: <b>$ parsed_wunder: </b><PRE>";
             echo '<h1>PARSED WUNDER</h1>';
            print_r($parsed_wunder_all);
            echo '<hr style="color:tomato">';
            echo '<h1>PARSED WUNDER DATA ALL - ALLDATA</h1>';
             print_r($parsed_wunder);
            echo '<hr style="color:tomato">';
             echo '<h1>PARSED OPENDATA</h1>';
             print_r($parsed_open);
            echo '<hr style="color:tomato">';
             echo '<h1>PARSED OPENDATA FORECAST</h1>';
            print_r($parsed_forecast);
            echo '<hr style="color:tomato">';
            exit;
        }

        if(isset($_GET['debug'])){
            include('debug.php');
            exit;
        }

        $almanac=$parsed_wunder_all->almanac;
        $almanac_high=$almanac->temp_high;
        $almanac_low=$almanac->low_high;

        $satellite=$parsed_wunder_all->satellite;
        $image_url=$satellite->image_url;
        $image_url_=str_replace('&gtt=0', '', $image_url);

        $image_url_ir4=$satellite->image_url_ir4;
        $image_url_ir4_=str_replace('&gtt=0', '', $image_url_ir4);

        $image_url_vis=$satellite->image_url_vis;
        $image_url_vis_=str_replace('&gtt=0', '', $image_url_vis);


        //SUN PHASE
        $moon_phase = $parsed_wunder->moon_phase;
        $percentIlluminated = $moon_phase->percentIlluminated;
        $percentIlluminated_js=$percentIlluminated*.01;
        $ageOfMoon = $moon_phase->ageOfMoon;
        $phaseofMoon = $moon_phase->phaseofMoon;
        $hemisphere = $moon_phase->hemisphere;
        if(strlen(strstr(strtolower($phaseofMoon),'waxing'))>0) {
            $phaseofMoon_ww=1;   //true = waxing - shadow on the left
        }else{
            $phaseofMoon_ww=0;  //false = waning - shadow on the right
        }

        $moon_phase_string='PercentageIlluminated: '.$percentIlluminated.'% / '.$percentIlluminated_js;
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
        $uv_index = $parsed_wunder->current_observation->UV;



        //WIND
        $wind_string = $parsed_wunder->current_observation->wind_string;
        $wind_dir = $parsed_wunder->current_observation->wind_dir;
        $wind_degrees = $parsed_wunder->current_observation->wind_degrees;
        $wind_mph = $parsed_wunder->current_observation->wind_mph;
        $wind_gust_mph = $parsed_wunder->current_observation->wind_gust_mph;

/*
echo '$wind_string='.$wind_string.'<BR>';
echo '$wind_dir='.$wind_dir.'<BR>';
echo '$wind_degrees='.$wind_degrees.'<BR>';
echo '$wind_mph='.$wind_mph.'<BR>';
echo '$wind_gust_mph='.$wind_gust_mph.'<BR>';

$wind_string=Calm
$wind_dir=SSW
$wind_degrees=207
$wind_mph=0
$wind_gust_mph=3.5
*/
        //RAIN
        $precip_1hr_string = $parsed_wunder->current_observation->precip_1hr_string;
        $precip_1hr_in = $parsed_wunder->current_observation->precip_1hr_in;
        $precip_today_string = $parsed_wunder->current_observation->precip_today_string;
        $precip_today_in = $parsed_wunder->current_observation->precip_today_in;
        $leaf_wetness = $parsed_wunder->current_observation->leaf_wetness;

/*

echo '$precip_1hr_in='.$precip_1hr_in.'<BR>';
echo '$precip_today_in='.$precip_today_in.'<BR>';
echo '$leaf_wetness='.$leaf_wetness.'<BR>';
exit;

 */
        $icon = $parsed_wunder->current_observation->icon;
        $icon_url = $parsed_wunder->current_observation->icon_url;
        $forecast_url = $parsed_wunder->current_observation->forecast_url;
        $history_url = $parsed_wunder->current_observation->history_url;
        $ob_url = $parsed_wunder->current_observation->ob_url;

    echo '<div class="weatherContainer">';
        echo '<div class="weatherCurrent">';
            echo '<div class="weatherTemp">';
                echo $temp_f.'<span class="degSmall">&deg;F</span>';
                echo '<span class="weather_minmax" min="'.$temp_min.'" max="'.$temp_max.'">'.$temp_min.'&deg;F/'.$temp_max.'&deg;F</span>';
                //echo '<div class="feelsLike">'.$feelslike_string.'</div>';
            echo '</div>';


            echo '<div class="windGust">';
                echo '<canvas id="windGustCanvas" width="100" deg="'.$wind_degrees.'"height="100"></canvas>';
                echo '<div class="dialFont">'.$wind_mph.'<span>mi/h</span></div>';
                echo '<div class="dialFont windGusts">'.$wind_gust_mph.'<span>mi/h</span></div>';
                echo '<div style="width: 135px;">'.$wind_string.'</div>';
                //rain
                echo '<div id="rainGuageContainer" class="dialFont">';
                    echo $precip_1hr_in.'/'.$precip_today_in.' in<br>lw: '.$leaf_wetness;
                echo '</div>';
            echo '</div>';

/*
<div id="moonPhase" percent="82" percentilluminated_js="0.82" phase="1" class="moonPhase" style=""><div style="position: absolute; height: 50px; width: 50px; border: 1px solid rgb(62, 60, 60); background-color: black; border-radius: 25px; overflow: hidden;"><div style="position: absolute; background-color: rgb(153, 187, 255); border-radius: 25.0313px; height: 50.0625px; width: 50.0625px; left: 11.5px; top: -0.03125px; box-shadow: rgb(153, 187, 255) 0px 0px 5px 5px; opacity: 0.9;"></div></div></div><em>PercentageIlluminated: 82% / 0.82</em><em>Waxing Gibbous North H</em><em>Age of Moon: 11</em></div>
*/

            //UV INDEX
            $uv_index_round=round($uv_index);
            $uv[0]='';
            $uv[1]='';
            $uv[2]='';
            $uv[3]='';
            $uv[4]='';
            for ($i=0; $i < $uv_index_round; $i++) {
                $uv[$i]='low';
            }

            echo '<div class="uvIndex">';
               echo '<uv-index-pyramid _ngcontent-c9="" _nghost-c18="">';
                   echo '<div _ngcontent-c18="" class="pyramid">';
                       echo '<div _ngcontent-c18="" class="chunk '.$uv[4].'"></div>';
                       echo '<div _ngcontent-c18="" class="chunk '.$uv[3].'"></div>';
                       echo '<div _ngcontent-c18="" class="chunk '.$uv[2].'"></div>';
                       echo '<div _ngcontent-c18="" class="chunk '.$uv[1].'"></div>';
                       echo '<div _ngcontent-c18="" class="chunk '.$uv[0].'"></div>';
                   echo '</div>';
               echo '</uv-index-pyramid>';
               echo '<div class="uv_index_label dialFont">'.$uv_index.'<span>UV</span></div>';
               echo '<div class="relative_humidity dialFont">'.$relative_humidity.'<span>H</span></div>';
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
                    echo '<div id="moonPhase" percent="'.$percentIlluminated.'" percentIlluminated_js="'.$percentIlluminated_js.'" phase="'.$phaseofMoon_ww.'" class="moonPhase" style=""></div>';
                    //echo '<em>'.$moon_phase_string.'</em>';
                    //echo '<em>'.$phaseofMoon_string.'</em>';
                    echo '<span class="moonAge">Age of Moon: <b>'.$ageOfMoon.'</b></span>';
                echo '</div>';//moonPhaseContainer
            echo '</div>';//weatherBaromoter



            echo '<div class="weatherUpdateDate">';
                echo date('D M j g:i:s A',$local_epoch);
            echo '</div>';
        echo '</div>';//weatherCurrent

        include('forecast.php');

        echo '<div class="satteliteCont">';

	echo '<div class="sat0" style="height: 155px;">';
		echo '<img id="radarMoving" class="" src="//icons.wxug.com/data/weather-maps/radar/united-states/united-states-current-radar-animation.gif" style="height: 181px;position: relative;top: -10px;">';
	echo '</div>';

	//echo '<div class="sat0"><img id="radarMoving" class="" src="//icons.wxug.com/data/weather-maps/radar/united-states/united-states-current-radar-animation.gif"></div>';

            echo '<div class="sat1">';
             echo '<img id="radarMoving" class="" src="http://api.wunderground.com/api/a4b1e907bb43a8dc/animatedradar/animatedsatellite/q/CT/Newington.gif?num=10&delay=30&width=600&interval=30&sat.width=640&sat.height=480&sat.key=sat_ir4_bottom&sat.gtt=107&sat.proj=me&sat.timelabel=0">';
            echo '</div>';

            echo '<div class="sat2 noshow">';
             echo '<img src="'.$image_url_ir4_.'">';
            echo '</div>';

            echo '<div class="sat3">';
             echo '<img src="'.$image_url_vis_.'">';
            echo '</div>';


            echo '<div class="fullScreenButton noshow">';
             echo '<input type="button" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;fullscreen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" onclick="toggleFullScreen()">';
            echo '</div>';
        echo '</div>';
        echo '<div class="almanacRecordContainer">';
            echo '<div class="almanacRecordHigh">';
                echo "<div class='highYear'>HIGHEST: <b>".$almanac->temp_high->recordyear."</b></div>";
                echo '<div class="weatherMinMaxLabelContainer">';
                    echo '<span class="weatherMinMaxLabel">';
                        echo '<span class="top" style="">Normal</span>';
                        echo '<span class="bottom" style="width: 47px;">Record</span>';
                    echo '</span>';

                    echo '<div class="highNormRecrd">';
                        echo '<span class="weather_minmax" min="'.$almanac->temp_high->normal->F.'" max="'.$almanac->temp_high->record->F.'">'.$almanac->temp_high->normal->F.'&deg;F/'.$almanac->temp_high->record->F.'&deg;F</span>';
                    echo '</div>';//weatherMinMaxLabel
                echo '</div>';//weatherMinMaxLabelContainer
                //echo "<div class='highYear'>".$almanac->temp_high->recordyear.'</div>';
            echo '</div>';//almanacRecordHigh


            echo '<div class="almanacRecordLow">';
                echo "<div class='lowYear'>LOWEST: <b>".$almanac->temp_low->recordyear."</b></div>";
                echo '<div class="weatherMinMaxLabelContainer">';
                    echo '<span class="weatherMinMaxLabel">';
                        echo '<span class="top" style="">Normal</span>';
                        echo '<span class="bottom" style="width: 47px;">Record</span>';
                    echo '</span>';

                    echo '<div class="highNormRecrd">';
                         echo '<span class="weather_minmax" min="'.$almanac->temp_low->normal->F.'" max="'.$almanac->temp_low->record->F.'">'.$almanac->temp_low->normal->F.'&deg;F/'.$almanac->temp_low->record->F.'&deg;F</span>';
                    echo '</div>';
                echo '</div>';
                //echo "<div class='lowYear'>".$almanac->temp_low->recordyear.'</div>';
            echo '</div>';//almanacRecordLow
        echo '</div>';//almanacRecordContainer




    }
}
echo '</div>';//weatherContainer

echo '<div>';//weatherContainer
echo '<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>';//weatherContainer
echo '</div>';//weatherContainer
$result->close();
mysqli_close($con);

echo '</body>';
echo '</html>';

?>
