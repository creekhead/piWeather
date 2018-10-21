<?php

echo '<div class="futureForecast">';

    $count=0;
    $start=false;
    $skip_8pm=false;
    foreach ($parsed_forecast->list as $key => $forecast_day) {

        $date=date('h:i A',$parsed_forecast->list[$key]->dt);

//echo '<hr style="color:tomato">';;
//echo "<h2>".$date."</h2>";
//echo '<hr style="color:tomato">';

        if(!strlen(strstr($date,'08:00 AM'))>0 && !strlen(strstr($date,'02:00 PM'))>0) {
            if($skip_8pm){
                $skip_8pm=false;
                continue;
            }
            continue;
        }

        //if 2PM is shown then skip the next 8PM
        if(strlen(strstr($date,'02'))>0){
            $skip_8pm=true;
        }

        $count++;

        if($count % 2 !==0){
            $day=0;
            echo '<div class="forecastDay">';
        }else{
            $day=1;
        }


            echo '<div id="key_'.$key.'" class="forecastContainer">';
                echo '<div class="weather_temp_container">';

                    echo '<div class="weather_tempmin_container">';
                        echo '<b class="weather_temp">';
                            echo round($forecast_day->main->temp).'<span class="degSmall forecastdegSmall">&deg;F</span>';
                        echo '</b>';

                        echo '<span class="weather_minmax">'.round($forecast_day->main->temp_min).'&deg;F/'.round($forecast_day->main->temp_max).'&deg;F</span>';
                        //echo '<sup>'.round($forecast_day->main->temp_min).'&deg;F</sup>/<sub>'.round($forecast_day->main->temp_max).'&deg;F</sub>';
                    echo '</div>';  //weather_tempmin_container

                    echo '<img class="forecastImage" src="img/'.$forecast_day->weather[0]->icon.'.png">';

                echo '</div>';


                echo '<div class="weather_main">';
                    //echo ucwords($forecast_day->weather[0]->main);
                    echo '<div class="weather_desc">';
                        echo ucwords($forecast_day->weather[0]->description);
                    echo '</div>';
                echo '</div>';


                echo '<span class="forecastDateContainer">';
                    echo '<span>'.date('l',$parsed_forecast->list[$key]->dt).'</span>';
                    echo '<span>'.date('M j h:i A',$parsed_forecast->list[$key]->dt).'</span>';
                echo '</span>';

            echo '</div>';  //forecastContainer

        if($day==1){
            echo '</div>';//forecastDay
        }
    }

echo '</div>';//futureForecast

?>

