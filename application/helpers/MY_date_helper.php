<?php

function custom_datetime($format, $time)
{

	$yesterday = strtotime("yesterday");
	$today = strtotime("today");
	$tomorrow = strtotime("tomorrow");
	$after_tomorrow = $tomorrow + 24*60*60;
	
	if ($time == 0)
		return lang("not-recorded");

	if (($time >= $yesterday) && ($time < $today))
		return lang("date-yesterday") . " " . date("H:i", $time);

	if (($time >= $today) && ($time < $tomorrow))
		return lang("date-today") . " " . date("H:i", $time);

	if (($time >= $tomorrow) && ($time < $after_tomorrow))
		return lang("date-tomorrow") . " " . date("H:i", $time);

	return date($format, $time);
}

?>
