<?php


function totalTime($time)
{
    $TIME = explode(":", $time);
    return intval($TIME[0])." hr ".intval($TIME[1])." min";
}

function totalTimeCalculated($time)
{
    $TIME = explode(":", $time);
    $hours = intval($TIME[0]);
    $mins = intval($TIME[1])/60;
    $partHour = round($mins, 1)*10;
    if ($partHour == 10)
    {
        $hours++;
        $partHour = 0;
    }
    return $hours.".".$partHour." hrs";
}

function x_week_range($date)
{
    $ts = strtotime($date);
    $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
    return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next saturday', $start)));
}

function decimal_numbers($_input)
{ 
    $number = (float) preg_replace('/[^0-9.]*/','', $_input);
    return number_format( (float) $number, 2, '.', '');
}

?>