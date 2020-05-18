<?php
class Content extends Activity
{
	private static function __load_country__(String $countryname)
	{
		$file = IO::root("var/$countryname/meta.json");
		if(!is_file($file)) return Core::response([], "no world serie found...");
		return IO::jout($file);
	}

	public static function series(String $countryname){
		return Convert::json(self::__load_country__($countryname));
	}

	public function totals(String $countryname){
		$return = "";
		$path = IO::root("var/$countryname");
		if(is_dir($path)){
			$country = new stdClass;
			$states = IO::folders($path);
			$country->{"TOTAL"} = IO::jout("$path/total.json");			
			Vector::each($states, function($state) use ($path, &$country){ $country->{$state} = IO::jout("$path/$state/total.json"); });
			$return = Convert::json($country);
		}else $return = Core::response(-1, "no country folder found");
		return $return;
	}

	public function csir(String $countryname){
		$path = IO::root("var/$countryname");
		if(is_dir($path)) $return = IO::jout("$path/total.json");
		else return Core::response(-1, "no country folder found");
		print_r($return);die;
		if(!isset($return->csir)) return Core::response(-2, "no SIR done yet");
		return $return->csir;
	}

	public function states(String $countryname){
		$return = "";
		$path = IO::root("var/$countryname");
		if(is_dir($path)) $return = Convert::json(IO::folders($path));
		else $return = Core::response(-1, "no country folder found");
		return $return;
	}

};
