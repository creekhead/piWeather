<?php
    //WEATHER Variables
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