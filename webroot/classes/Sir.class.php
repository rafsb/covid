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
	private $r        = 2.5;
	
	public $latency   = 6;
	public $period    = 19;
	public $mortality = .017;
	public $affected_pop_factor = .75;

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
		$s_a = &$this->susceptible_array;
		$i_a = &$this->infected_array;
		$r_a = &$this->recovered_array;
		$d_a = &$this->deaths_array;
		$s = &$this->susceptible;
		$i = &$this->infected;
		$r = &$this->recovered;
		$d = &$this->deaths;

		$l = $this->susceptible;
		$p = $this->period;
		$m = $this->mortality;

		$s_a[] = $s;
		$i_a[] = $i;
		$r_a[] = $r;
		$d_a[] = $d;

		$line    = [];
		$daily_i = [];
		$daily_d = [];

		$mult = $this->r / $this->latency;
		$max = $this->affected_pop_factor;

		// echo $this->r . ", " . $this->latency . "PHP_EOL";

		Loop::iterate(0, $this->length, function($iter) use (&$s, &$i, &$r, &$d, &$s_a, &$i_a, &$r_a, &$d_a, &$line, &$daily_i, &$daily_d, $p, $l, $m, $mult, $max){

			$rec = 0;

			if($iter >= $p){
				$rec = $i_a[$iter - $p]; 
			 	$d = ($rec * $m);
			 	$r = $rec - $d;
			}

			$s = $l - $i;
			$i += $i * $mult * $s / $l;
			
			$s_a[] = floor($s);
			$i_a[] = floor($i);
			$r_a[] = floor($r);
			$d_a[] = floor($d);

			$line[] = floor($i - $r - $d);
			$daily_i[] = $iter ? $i_a[$iter] - $i_a[$iter - 1] : $i_a[$iter];
			$daily_d[] = $iter ? $d_a[$iter] - $d_a[$iter - 1] : $d_a[$iter];

			//echo "$mult\n";
		});

		return Convert::atoo([
			"susceptible" => $s_a
			, "infected"  => $i_a
			, "recovered" => $r_a
			, "deaths"    => $d_a
			, "line"      => $line
			, "daily_infected" => $daily_i
			, "daily_deaths"   => $daily_d
		]);
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
}