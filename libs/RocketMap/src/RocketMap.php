<?php

namespace RocketMap;

class Team {
	const Mystic = 1;
	const Valor = 2;
	const Instinct = 3;
}

class Base {
	public function __construct($data = NULL){
		if(!is_array($data)){ return; }
		foreach($data as $k => $v){
			if(property_exists(get_class($this), $k)){
				$this->$k = $v;
			}
		}
	}
}

class Gym extends Base {
	public function __construct($data = NULL){
		parent::__construct($data);
		if(!empty($this->gym_id)){
			$this->gym_id = base64_decode($this->gym_id);
			$this->last_scanned = strtotime("now");
			$this->last_modified = round($this->last_modified / 1000);
		}
		if(array_key_exists('team', $data)){
			$this->team_id = $data['team'];
		}
	}

	public $gym_id;
	public $team_id;
	public $guard_pokemon_id;
	public $slots_available = 6;
	public $enabled = TRUE;
	public $latitude;
	public $longitude;
	public $total_cp = 0;
	public $last_modified;
	public $last_scanned;
}

class GymDetails extends Base {
	public $gym_id;
	public $name;
	public $description;
	public $url;
	public $last_scanned;
}

class GymMember extends Base {
	public $gym_id;
	public $pokemon_uid;
	public $last_scanned;
	public $deployment_time;
	public $cp_decayed = 0;
}

class GymPokemon extends Base {
	public $pokemon_uid;
	public $pokemon_id;
	public $cp = 0;
	public $trainer_name;
	public $num_upgrades = 0;
	public $move_1;
	public $move_2;
	public $height;
	public $weight;
	public $stamina;
	public $stamina_max;
	public $cp_multiplier;
	public $additional_cp_multiplier;
	public $iv_defense;
	public $iv_stamina;
	public $iv_attack;
	public $last_seen;
}

class Raid extends Base {
	public $gym_id;
	public $level;
	public $spawn;
	public $start;
	public $end;
	public $pokemon_id;
	public $cp;
	public $move_1;
	public $move_2;
	public $latitude;  // ?
	public $longitude; // ?
	public $last_scanned;
}

?>