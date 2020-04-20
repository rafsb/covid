<?php
class Transform extends Activity
{
	private static function load_list()
	{
		return Vector::extract(IO::jout("etc/data-retrievment.json"), function($data){ return $data->country; });
	}

	private static function parse(String $countryname)
	{
		$folder = IO::root("var/$countryname");
		if(!is_dir($folder)) mkdir($folder, 0777, true);

		$filename = Vector::extract(IO::jout("etc/data-retrievment.json"), function($data) use ($countryname){ return $data->country == $countryname ? $data->name : null; })[0];
		$extension = substr($filename, strlen($filename)-4);

		// parsing content
		$content = IO::read("var/$countryname/$filename");
		if($extension == "json") $content = Convert::json($content);
		else if($extension == ".csv") $content = Vector::extract(preg_split("/\s+/ui",$content), function($line){ return preg_split("/[,]/ui", $line); });
		
		// default object
		$country = Convert::atoo([
			"country" 		=> $countryname
			, "ldate" 		=> time()
			, "confirmeds" 	=> []
			, "deaths" 		=> []
			, "inner_serie" 	 => []
		]);
		// load the custom transform function for especific country
		$parser = IO::root("etc/parser.d/$countryname.php");
		if(is_file($parser)) include $parser;

		IO::jin("var/$countryname/meta.json", $country);
	}

	public static function all()
	{
		Vector::async(self::load_list(), function($country){ Transform::parse($country); });
	}

	public static function only(String $country)
	{
		if(in_array($country, Vector::extract(self::load_list(), function($data){ return $data; }))) self::parse($country);
		return Core::response(-1, "$country not found!");
	}
}