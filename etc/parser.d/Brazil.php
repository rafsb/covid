<?php
/*
 * $folder
 * $filename
 * $extension
 * $content
 * $country
 */

/*
 * Transform CSV into JSON for better interation
 */

echo PHP_EOL;

Vector::each($content, function($line, $i) use (&$country){ 
	
	// Just valid data accept
	if($i&&isset($line[3])){

		// get fields from CSV
		$date 				 	= isset($line[0])  ? $line[0]  : date('Y-m-d');
		$state 				 	= isset($line[2])  ? $line[2]  : 'UNIÃO';
		$confirmed	 	 		= isset($line[8])  ? max(0, $line[8])  : 0;
		$confirmed_per_day		= isset($line[7])  ? max(0, $line[7])  : 0;
		$confirmed_per_100k_hab = isset($line[10]) ? max(0, $line[10]) : 0;
		$deaths				 	= isset($line[6])  ? max(0, $line[6])  : 0;
		$deaths_per_day		 	= isset($line[5])  ? max(0, $line[5])  : 0;
		$deaths_per_100k_hab	= isset($line[9])  ? max(0, $line[9])  : 0;
		$deaths_perconfirmed	= isset($line[11]) ? max(0, $line[11]) : 0;
		$name 				 	= explode(DS, $line[3])[0] . DS . $state;
		// $population 			= in_array($name, $population_names_array)&& $name!='TOTAL/TOTAL'? $population_array->{$name} : 1;

		// echo ' => translating (csv2json): ' . $name . '\r';

		// sum the total population that is visible by the virus
		// $virus_population += $population;

		// set state structure
		if(!isset($country["states"])) $country["states"] = [];
		if(!isset($country["states"][$state])) $country["states"][$state] = [ ];
		
		// create new or inherit from exixting city structure
		if(!isset($country["states"][$state][$name])) $city = [ ];
		else $city = $country["states"][$state][$name];

		// if(!isset($city['pop'])) $city['pop'] = $population;
		// else $city['pop'] = $population;

		// set city's fields
		if(!isset($city['state'])) $city['state'] = $state;
		if(!isset($city['series'])) $city['series'] = [];

		// create am empty city temporal serie node if not exists
		if(!isset($city['series'][$date])) $city['series'][$date] = [
			'c' 		=> 0
			, 'dc' 	=> 0
			, 'd' 		=> 0
			, 'dd' 	=> 0
		];

		// if(!isset($city["csir"])) $city["csir"] = Sir::serie($population);

		// fill each temporal serie node
		$city['series'][$date]['c']  += $confirmed;;
		$city['series'][$date]['dc'] += $confirmed_per_day;
		$city['series'][$date]['d']  += $deaths;
		$city['series'][$date]['dd'] += $deaths_per_day;

		// assign temporary city data to main variable
		$country["states"][$state][$name] = $city;

	// print no valid data
	}

});

echo PHP_EOL;


$population_array = IO::jout("var/Brazil/population.json");
$population_names_array = array_keys((array)$population_array);
$virus_population = 0;
$states = (array)$country["states"];

Vector::each($states, function($statedata, $statename) use (&$country, &$virus_population, $population_array, $population_names_array) {
 	Vector::each($statedata, function($citydata, $cityname) use (&$country, &$virus_population, $population_array, $population_names_array, $statename){ 
 		// echo ' => assign population and cSIR: ' . $cityname . '\r';
 		$citydata['pop'] = in_array($cityname, $population_names_array) && $cityname!='TOTAL/TOTAL' ? $population_array->{$cityname} : 1;
 		$virus_population += $citydata['pop'];
 		$country["states"][$statename][$cityname]["csir"] = Sir::serie($citydata["pop"], 3.4, .2, 100, Vector::extract($citydata["series"], function($d){ return $d["d"]&&$d["d"]*1 ? $d["d"] : null; }));
 		// if($cityname=="São Paulo/SP"){ 
 		// 	echo $citydata["pop"];
 		// 	// print_r(Vector::extract($citydata["series"], function($d){ return $d["d"]&&$d["d"]*1 ? $d["d"] : null; }));
 		// 	print_r(Sir::serie($citydata["pop"], 3.4, .25, 100, Vector::extract($citydata["series"], function($d){ return $d["d"]&&$d["d"]*1 ? $d["d"] : null; }), false)); 
 		// 	die; 
 		// }
 	});
});

$country['pop'] = $virus_population;

$csir = null;
Vector::each($states, function($statedata, $statename) use (&$csir, $country) {
 	Vector::each($statedata, function($citydata, $cityname) use (&$csir, $country, $statename){ 
 		$tmp = $country["states"][$statename][$cityname]["csir"];
 		if(!$csir) $csir = $tmp;
 		else {
 			if($tmp) Vector::each($tmp, function($arr, $name) use(&$csir){
 				Vector::each($arr, function($n, $i) use(&$csir, $name){
 					$csir[$name][$i] += max(0, $n*1);
 				});
 			});
 		}
 	});
});

$country['csir'] = $csir;
IO::jin("var/Brazil/csir.json", $csir);

echo PHP_EOL;

/*
 * the raw data of the country is now too big
 * so, we'll split into small satte's files
 * it will reduce impact on UX
 */
Vector::async($country["states"], function($statedata, $statename){
	
	// only accept valid data
	if(!is_array($statedata)) return;

	// show current state in chain
	echo ' => spliting documents: ' . $statename . PHP_EOL;
	
	/* check if is a real state or the totalization cell
	 * the totalization cell is ready to be written to disk
	 * but will not be separated in a folder
	 */
	if($statename == 'TOTAL') {
		
		$path = 'var/Brazil';
		
		// get total sequence of the coutry
		$statedata = $statedata["TOTAL/TOTAL"]["series"];
		
		// generated the great file that contains all citis within the state
		IO::jin($path . '/total.json', $statedata);

	// stetes too are ready, but have no totalization cell for itself
	} else {

		// the state cell will be placed into a state folder
		$path = 'var/Brazil/' . $statename;

		// generate an empty totalization cell
		$state_total_info = [];

		// each state have cities
		Vector::each($statedata, function($citydata, $cityname) use (&$state_total_info) {

			// each city have its own serie
			Vector::each($citydata['series'], function($citydata, $citydate) use (&$state_total_info){

				// fill the temporal cell if empty
				if(!isset($state_total_info[$citydate])) $state_total_info[$citydate] = [
					'c' 	=> 0
					, 'dc' 	=> 0
					, 'd' 	=> 0
					, 'dd' 	=> 0
				];

				// assign or somatize values
				$state_total_info[$citydate]['c'] 	+= $citydata['c'];
				$state_total_info[$citydate]['dc'] 	+= $citydata['dc'];
				$state_total_info[$citydate]['d'] 	+= $citydata['d'];
				$state_total_info[$citydate]['dd'] 	+= $citydata['dd'];

			});
		});

		// create state folder if not exists
		IO::mkd($path);
		
		// generate separetad state totalization cell file
		IO::jin($path . '/total.json', $state_total_info);
		
		// generated the great file that contains all citis within the state
		IO::jin($path . '/meta.json', $statedata);
	}

});

echo PHP_EOL;