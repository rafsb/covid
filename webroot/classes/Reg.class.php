<?php
class Reg extends Activity {

	public function save(String $anything, String $where="var/logs/reg.log"){
		if($anything){
			$tmp = IO::read($where);
			$tmp .= "\n$anything\n";
			return IO::write($where, $tmp);
		}
	}

}