<?php

require 'config.php';
require 'config-rocket.php';
require 'libs/Telegram-PHP/src/Autoloader.php';
require 'libs/RocketMap/src/RocketMap.php';

$content = file_get_contents("php://input");
$json = json_decode($content, TRUE);

if(empty($json)){ die(); }

$bot = new Telegram\Bot($config['telegram']);
$telegram = new Telegram\Sender($bot);
$telegram->convert_emoji = FALSE;

$gyms = array();
$color = ['Neutro', 'Azul', 'Rojo', 'Amarillo'];
$pokedex = require 'libs/RocketMap/src/Pokedex.php';

foreach($json as $message){
	if(!in_array($message['type'], $rocket['parse_types'])){ continue; }
	if($message['type'] == "gym"){
		$gym = new \RocketMap\Gym($message['message']);

		$title = "Gimnasio de color " .$color[$gym->team_id] ."!";
		$sub = $gym->slots_available ." huecos";
		$telegram
			->chats($rocket['parse_chats'])
			->location($gym->latitude, $gym->longitude)
			->venue($title, $sub)
		->send();
	}elseif($message['type'] == "raid"){
		$raid = new \RocketMap\Raid($message['message']);

		$title = "¡Raid " .$pokedex[$raid->pokemon_id][0] ."! " .$raid->cp ." PC.";
		$sub = "Termina a " .date("H:i:s", $raid->end);

		$telegram
			->chats($rocket['parse_chats'])
			->location($raid->latitude, $raid->longitude)
			->venue($title, $sub)
		->send();
	}elseif($message['type'] == "gym_details"){
		$gym = new \RocketMap\Gym($message['message']);
		$details = new \RocketMap\GymDetails($message['message']);
		$pokemon = array();
		if(!empty($message['message']['pokemon'])){
			foreach($message['message']['pokemon'] as $pk){
				$pokemon[] = new \RocketMap\GymPokemon($pk);
			}
		}

		$str = "¡Nuevo gym " .$color[$gym->team_id] ."! " .$details->name ."\n"
				."Hay " .count($pokemon) ." Pokémon.\n";
		foreach($pokemon as $pk){
			$iv = number_format(($pk->iv_defense + $pk->iv_attack + $pk->iv_stamina) / 45 * 100, 1);
			$str .= $pokedex[$pk->pokemon_id][0] ." " .$pk->cp ." PC - $iv %\n";
		}

		$telegram->chats($rocket['parse_chats'])->file('photo', $details->url);
		$telegram
			->chats($rocket['parse_chats'])
			->text($str)
		->send();
	}elseif($message['type'] == "pokemon"){
		$pokemon = new \RocketMap\Pokemon($message['message']);
		$iv = number_format(($pokemon->individual_defense + $pokemon->individual_attack + $pokemon->individual_stamina) / 45 * 100, 1);

		$title = "¡Un " .$pokedex[$pokemon->pokemon_id][0] ."! ";
		$sub = "Se va a las " .date("H:i:s", $pokemon->disappear_time) .".";
		$telegram
			->chats($rocket['parse_chats'])
			->location($pokemon->latitude, $pokemon->longitude)
			->venue($title, $sub)
		->send();
	}
}
?>
