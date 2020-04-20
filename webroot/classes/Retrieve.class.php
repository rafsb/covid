<?php
class Retrieve extends Activity
{
	public static function data()
	{
		// get json of countries to be monitored, also worlds series
		$countries = IO::jout("etc/data-retrievment.json");

		// async method to retrieve data due to batch like utilization f this class
		Vector::async($countries, function($country){
			
			Fetch::request($country->url, function($data) use ($country){

				$dir = "var/" . $country->country . "/";
				if(!is_dir($dir)) mkdir($dir, 0777, true);
				IO::write($dir . $country->name, $data);

			});

		});
		
	}
}