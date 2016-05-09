<?php

	define("API_VERSION", "6.9.1");

	switch ($_SERVER['SERVER_NAME']) {
		case "webetu.pau.eisti.fr":
			define("APP_ROOT", "http://webetu.pau.eisti.fr/~nussbaume/mychampionslist"); break;
		case "localhost":
			define("APP_ROOT", "http://webetu.eistiens.work/~nussbaume/mychampionslist"); break;
		default:
			define("APP_ROOT", "http://".$_SERVER['SERVER_NAME']); break;
	}

	$REGIONS_LIST = [
		"NA" => [ "name" => "North America", "id" => "NA1"],
		"EUW" => [ "name" => "Europe West", "id" => "EUW1"],
		"EUNE" => [ "name" => "Europe Nordic &amp; East", "id" => "EUN1"],
		"BR" => [ "name" => "Brazil", "id" => "BR1"],
		"KR" => [ "name" => "Korea", "id" => "KR"],
		"TR" => [ "name" => "Turkey", "id" => "TR1"],
		"RU" => [ "name" => "Russia", "id" => "RU"],
		"JP" => [ "name" => "Japan", "id" => "JP" ],
		"LAN" => [ "name" => "Latin America North", "id" => "LA1"],
		"LAS" => [ "name" => "Latin America South", "id" => "LA2"],
		"OCE" => [ "name" => "Oceania", "id" => "OC1"]
	]

?>