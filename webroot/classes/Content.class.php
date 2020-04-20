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
};
