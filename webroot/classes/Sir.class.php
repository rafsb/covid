<?php
class __Cell__
{
	private $susceptible = 1000;
	private $infected    = 1;
	private $recovered   = 0;
	private $deaths      = 0;

	private $susceptible_array = [];
	private $infected_array    = [];
	private $recovered_array   = [];
	private $deaths_array      = [];
	
	private $length	  = 360;
	private $r        = 1.02;
	
	public $latency   	 = 6;
	public $restore_time = 19;
	public $mortality    = .0017;

	public function pop(int $p=null){
		if($p !== null) $this->susceptible = $p;
		return $this->susceptible;
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
		$s_a = array_fill(0, $this->length, 0);
		$i_a = array_fill(0, $this->length, 0);
		$r_a = array_fill(0, $this->length, 0);
		$d_a = array_fill(0, $this->length, 0);

		$mainline	    = array_fill(0, $this->length, 0);
		$daily_infected = array_fill(0, $this->length, 0);
		$daily_deaths   = array_fill(0, $this->length, 0);
			
		$R            = 1 + $this->r / $this->latency;
		$mortality    = $this->mortality;
		$restore_time = $this->restore_time;
		$population   = $this->susceptible;

		$susceptible  = $this->susceptible;
		$infected     = 1;
		$recovered    = 0;
		$deaths   	  = 0;

		$yesterday = [
			"deaths" => 0
			, "infected" => 0
		];

		Loop::iterate(0, $this->length, function($iter) use (&$s_a, &$i_a, &$r_a, &$d_a	, &$susceptible, &$infected, &$recovered, &$deaths, $R, $mortality, $restore_time, $population, &$mainline, &$daily_infected, &$daily_deaths, &$yesterday){

			$infected = min($population, $infected * $R * $susceptible / $population);
			$susceptible = max(0, $population - $infected);
			
			if($iter >= $restore_time){
				$recovered = max($recovered, $i_a[$iter - $restore_time] - $d_a[$iter - $restore_time]);
				$deaths    += max(0, ($mainline[$iter - $restore_time] * $mortality));
			}

			$mainline[$iter] = $infected - $recovered - $deaths;
			if($iter == 0){
				$daily_infected[$iter] = $infected;
				$daily_deaths[$iter]   = $deaths;
			} else {
				$daily_infected[$iter] = $infected - $yesterday["infected"];
				$daily_deaths[$iter]   = $deaths - $yesterday["deaths"];
			}

			$s_a[$iter] = $susceptible;
			$i_a[$iter] = $infected;
			$r_a[$iter] = $recovered;
			$d_a[$iter] = $deaths;

			$yesterday["infected"] = $infected;
			$yesterday["deaths"] = $deaths;
		});

		$start = 0;
		while($mainline[$start] < 1000 && ++$start < sizeof($mainline));

		$end = sizeof($daily_deaths);
		while($daily_deaths[--$end]<10 && $end > 10);

		$this->susceptible_array = $start < $end ? array_slice($s_a, $start, $end-$start) : [];
		$this->infected_array    = $start < $end ? array_slice($i_a, $start, $end-$start) : [];
		$this->recovered_array   = $start < $end ? array_slice($r_a, $start, $end-$start) : [];
		$this->deaths_array      = $start < $end ? array_slice($d_a, $start, $end-$start) : [];
		$this->mainline          = $start < $end ? array_slice($mainline		, $start, $end-$start) : [];
		$this->daily_deaths      = $start < $end ? array_slice($daily_deaths	, $start, $end-$start) : [];
		$this->daily_infected    = $start < $end ? array_slice($daily_infected	, $start, $end-$start) : [];

		$return = [
			"susceptible" => $this->susceptible_array
			, "infected"  => $this->infected_array
			, "recovered" => $this->recovered_array
			, "deaths"    => $this->deaths_array
			, "line"    		=> $this->mainline
			, "daily_infected"  => $this->daily_infected
			, "daily_deaths"    => $this->daily_deaths
		];
		// print_r($return); die;

		return $return;
	}

	public static function make(int $pop=null, int $initial_r=null, int $max_days_to_predict=null){
		return (new __Cell__($pop, $initial_r, $max_days_to_predict))->gen();
	}

	public function __construct(int $pop=null, int $initial_r=null, int $max_days_to_predict=null){
		if($pop) $this->pop($pop);
		if($initial_r) $this->R($initial_r);
		if($max_days_to_predict) $this->limit($max_days_to_predict);
	}
}

class Sir extends Activity
{
	public function __cli(int $pop=null, int $initial_r=null, int $max_days_to_predict=null, $field="line")
	{
		print_r((new Sir($pop, $initial_r, $max_days_to_predict)));
		//print_r((new Sir($pop, $initial_r, $max_days_to_predict))->gen()->{$field});
	}

	public static function serie(int $pop=null, int $initial_r=null, int $max_days_to_predict=null,array $known_deaths=[]){
		$sir = __Cell__::make($pop, $initial_r, $max_days_to_predict);
		return $sir;
	}

	public static function web(int $pop=null, int $initial_r=null, int $max_days_to_predict=null,array $known_deaths=[]){
		return Convert::json(__Cell__::make($pop, $initial_r, $max_days_to_predict));
	}
}