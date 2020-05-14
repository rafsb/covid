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

$population_array = IO::jout("var/Brazil/population.json");
$population_names_array = array_keys((array)$population_array);

$virus_population = 0;

Vector::each($content, function($line, $i) use (&$country, &$virus_population, $population_array, $population_names_array){ 
	
	// Just valid data accept
	if($i&&isset($line[3])){

		// get fields from CSV
		$_Date_ 				 = isset($line[0])  ? $line[0]  : date('Y-m-d');
		$_State_ 				 = isset($line[2])  ? $line[2]  : 'UNIÃO';
		$_Confirmed	 	 		 = isset($line[8])  ? max(0, $line[8])  : 0;
		$_Confirmed_per_day		 = isset($line[7])  ? max(0, $line[7])  : 0;
		$_Confirmed_per_100k_hab = isset($line[10]) ? max(0, $line[10]) : 0;
		$_Deaths				 = isset($line[6])  ? max(0, $line[6])  : 0;
		$_Deaths_per_day		 = isset($line[5])  ? max(0, $line[5])  : 0;
		$_Deaths_per_100k_hab	 = isset($line[9])  ? max(0, $line[9])  : 0;
		$_Deaths_per_confirmed	 = isset($line[11]) ? max(0, $line[11]) : 0;
		$_Name 				 	 = explode(DS, $line[3])[0] . DS . $_State_;
		$_Population 			 = in_array($_Name, $population_names_array)&& $_Name!='TOTAL/TOTAL'? $population_array->{$_Name} : 1;

		// sum the total population that is visible by the virus
		$virus_population += $_Population;

		// set state structure
		if(!isset($country[$_State_])) $country[$_State_] = [ ];
		
		// create new or inherit from exixting city structure
		if(!isset($country[$_State_][$_Name])) $city = [ ];
		else $city = $country[$_State_][$_Name];

		if(!isset($city['pop'])) $city['pop'] = $_Population;
		else $city['pop'] += $_Population;

		// set city's fields
		if(!isset($city['state'])) $city['state'] = $_State_;
		if(!isset($city['series'])) $city['series'] = [];

		// create am empty city temporal serie node if not exists
		if(!isset($city['series'][$_Date_])) $city['series'][$_Date_] = [
			'c' 		=> 0
			, 'dc' 	=> 0
			, 'd' 		=> 0
			, 'dd' 	=> 0
		];

		if(!isset($city["csir"])) $city["csir"] = Sir::serie($_Population);

		// fill each temporal serie node
		$city['series'][$_Date_]['c'] 	+= $_Confirmed;;
		$city['series'][$_Date_]['dc'] 	+= $_Confirmed_per_day;
		$city['series'][$_Date_]['d'] 	+= $_Deaths;
		$city['series'][$_Date_]['dd'] 	+= $_Deaths_per_day;

		// assign temporary city data to main variable
		$country[$_State_][$_Name] = $city;

	// print no valid data
	}

});


$country['pop'] = $virus_population;

/*
 * the raw data of the country is now too big
 * so, we'll split into small satte's files
 * it will reduce impact on UX
 */
Vector::async($country, function($statedata, $statename){
	
	// only accept valid data
	if(!is_array($statedata)) return;

	// show current state in chain
	echo ' => parsing ' . $statename . PHP_EOL;
	
	// check if is a real state or the totalization cell
	// the totalization cell is ready to be written to disk
	// but will not be separated in a folder
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