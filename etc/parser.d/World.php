<?php
/*
 * $folder
 * $filename
 * $extension
 * $content
 * $country [
	  "country" 		  => $countryname
	  , "ldate" 		  => time()
	  , "confirmeds" 	  => []
	  , "deaths" 		  => []
	  , "inner_serie" 	  => []
  ];
 */

Vector::each((array)$content, function($data, $cname) use (&$country){
	
	$confirmeds = [];
	$deaths     = [];

	Vector::each($data, function($day) use (&$confirmeds, &$deaths, &$country){
		
		if(!isset($country->confirmeds[$day->date])) $country->confirmeds[$day->date] = 0;
		$country->confirmeds[$day->date] += $day->confirmed;
		$confirmeds[] = $day->confirmed;

		if(!isset($country->deaths[$day->date])) $country->deaths[$day->date] = 0;
		$country->deaths[$day->date] += $day->confirmed;
		$deaths[] = $day->deaths;

	});
	
	$country->inner_serie[] = [
		"name" 		   => $cname
		, "confirmeds" => $confirmeds
		, "deaths" 	   => $deaths
	];

});

$country->confirmeds = array_values($country->confirmeds);
$country->deaths     = array_values($country->deaths);