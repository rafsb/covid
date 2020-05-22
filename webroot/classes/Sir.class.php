<?php
class __Cell__
{
	private $susceptible = 1000;
	private $infected    = 1;
	private $recovered   = 0;
	private $deaths      = 0;

	private $susceptables_array = [];
	private $infected_array    = [];
	private $recovered_array   = [];
	private $deaths_array      = [];
	
	private $length	  = 360;
	private $r        = 2.4;
	
	public $latency   	 = 6;
	public $restore_time = 14;
	public $infected_cap_factor = .225;
	public $mortality    = .0017;

	public function pop(int $p=null){
		if($p !== null) $this->susceptible = $p;
		return $this->susceptible;
	}

	public function cap(float $p=null){
		if($p !== null) $this->infected_cap_factor = $p;
		return $this->infected_cap_factor;
	}

	public function limit(int $l=null){
		if($l!==null&&$l>0) $this->length = $l;
		return $this->length;
	}

	public function R(float $n=null){
		if($n) $this->r = $n;
		return $this->r;
	}

	public function gen(){
		
		$infected_cap_factor = $this->infected_cap_factor;

		// INFECTED
		$infected_array = [];
		$infected = 1;
		$daily_infected = [];
		$R = $this->r / $this->latency;
		$population  = $this->susceptible * $infected_cap_factor;
		Loop::iterate(0, $this->length, function() use (&$infected_array, &$infected, &$daily_infected, $R, $population){
			$tmp = $infected;
			$nr = 1 + max(0, $R * ($population - $infected) / $population);
			$infected *= $nr;
			$daily_infected[] = floor($infected - $tmp);
			$infected_array[] = floor($tmp);
		});

		// DEATHS
		$restore_time = $this->restore_time;
		$deaths_array = array_fill(0, $restore_time, 0);
		$deaths = 0;
		$daily_deaths = $deaths_array;
		$mortality    = $this->mortality;
		Loop::iterate($restore_time, $this->length, function($iter) use (&$deaths_array, &$deaths, &$daily_deaths, $mortality, $infected_array, $restore_time){
			$tmp = $infected_array[$iter - $restore_time];
			$tmp *= $mortality;
			$deaths_array[] = floor($tmp);
			$daily_deaths[] = floor($tmp - $deaths);
			$deaths = $tmp;
		});

		// RECOVERED
		$recovered_array = array_fill(0, $restore_time, 0);
		Loop::iterate(0, $this->length - $restore_time, function($iter) use (&$recovered_array, $infected_array, $deaths_array){
			$recovered_array[] = $infected_array[$iter] - $deaths_array[$iter];
		});

		// SUSCEPTABLES
		$susceptables_array = [];
		$population = $this->susceptible;
		Loop::iterate(0, $this->length, function($iter) use (&$susceptables_array, $recovered_array, $infected_array, $deaths_array, $population){
			$susceptables_array[] = $population - $infected_array[$iter];
		});

		// echo sizeof($infected_array) . " " .  sizeof($deaths_array) . " " .  sizeof($recovered_array); die;

		$mainline = [];
		Loop::iterate(0, $this->length, function($iter) use (&$mainline, $recovered_array, $infected_array, $deaths_array){
			$mainline[] = max(0, $infected_array[$iter] - $deaths_array[$iter] - $recovered_array[$iter]);
		});

		$start = 0;
		// while($mainline[$start] < 100 && ++$start < $this->length);

		$end = sizeof($daily_deaths)-1;
		// while($mainline[$end] < 100 && --$end > $start+10);

		$this->susceptables_array = array_slice($susceptables_array, $start, $end - $start);
		$this->infected_array    = array_slice($infected_array, $start, $end - $start);
		$this->recovered_array   = array_slice($recovered_array, $start, $end - $start);
		$this->deaths_array      = array_slice($deaths_array, $start, $end - $start);
		$this->line_array        = array_slice($mainline        , $start, $end - $start);
		$this->daily_deaths      = array_slice($daily_deaths	, $start, $end - $start);
		$this->daily_infected    = array_slice($daily_infected	, $start, $end - $start);

		$return = [
			"line"          => $this->line_array
			, "susceptible" => $this->susceptables_array
			, "infected"  	=> $this->infected_array
			, "recovered" 	=> $this->recovered_array
			, "deaths"    	=> $this->deaths_array
			, "daily_infected" => $this->daily_infected
			, "daily_deaths"   => $this->daily_deaths
		];
		// print_r($return); die;

		return $return;
	}

	public static function make(int $pop=null, int $initial_r=null,float $cap = null, int $max_days_to_predict=null){
		return (new __Cell__($pop, $initial_r, $cap, $max_days_to_predict))->gen();
	}

	public function __construct(int $pop=null, int $initial_r=null, float $cap=.45, int $max_days_to_predict=null){
		if($pop) $this->pop($pop);
		if($initial_r) $this->R($initial_r);
		if($cap) $this->cap($cap);
		if($max_days_to_predict) $this->limit($max_days_to_predict);
	}
}

class Sir extends Activity
{
	public function __cli(int $pop=null, int $initial_r=null, float $cap=null, int $max_days_to_predict=null)
	{
		print_r(__Cell__::make($pop, $initial_r, $cap, $max_days_to_predict));
	}

	public static function serie(int $pop=null, int $initial_r=null, float $cap=null, int $max_days_to_predict=null, array $known_deaths=[], bool $debug=false){
		$sir1 = __Cell__::make($pop, $initial_r, $cap, $max_days_to_predict);
		$sir2 = null;
		if($debug) print_r($known_deaths);
		if(sizeof($known_deaths)){
			$last_result = 0;
			$compare = Vector::extract($sir1["deaths"], function($v){ return $v*1 ? $v : null; });
			$known_deaths = Vector::extract($known_deaths, function($v){ return $v*1 ? $v : null; });
			$current_result = Vector::similarity($compare, $known_deaths);			
			if($debug) echo "tunning... $current_result" . PHP_EOL;
			while($current_result > $last_result && $initial_r > 1){
				if($debug)  echo " => " . $current_result;
				$last_result = $current_result*1;
				$initial_r -= .01;
				if($debug)  echo " <" . $initial_r;
				if($sir2) $sir1 = $sir2;
				$sir2 = __Cell__::make($pop, $initial_r, $cap, $max_days_to_predict);
				$compare = Vector::extract($sir2["deaths"], function($v){ return $v*1 ? $v : null; });
				$current_result = Vector::similarity($compare, $known_deaths);
				if($debug)  echo " *" . $current_result . PHP_EOL;
			}

		}
		return $sir1;
	}

}