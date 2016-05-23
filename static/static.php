<?php

	function check_input($data) {
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		$data = trim($data);
		return $data;
	}

	function getChampionKey($name) {
		switch ($name) {
			case "Cho'Gath": return "Chogath";
			case "Kha'Zix": return "Khazix";
			case "Vel'Koz": return "Velkoz";

			case "Fiddlesticks": return "FiddleSticks";
			case "LeBlanc": return "Leblanc";
			case "Wukong": return "MonkeyKing";
			
			default: return str_replace([" ",".","'"], "", $name);
		}
	}

	function formatNumber($n) {
		$str = (string) $n;
		$len = strlen($str) - 1;
		$res = "";

		for ($i = $len; $i >= 0; $i--) {
			$res = $str[$i] . ((($len-$i)%3)==0?",":"") . $res;
		}

		return substr($res, 0, -1);
	}

	function display_champions($CHAMPIONS) {
		global $CHAMPIONS_NAME, $FREE_TO_PLAY;

		$maxpoints = $CHAMPIONS[0]['championPoints'];

		$championMasteryScore = 0;
		$totalChampionsPoints = 0;
		$championsPlayed = count($CHAMPIONS);
		$numberOfChampions = count($CHAMPIONS_NAME);
		$level5ChampionsPlus = 0;
		$level4Champions = 0;

		$playedChamps = [];

		// graphChampions
		$tabs = str_repeat("	", 5);
		$graphChampions = "";
		$graphChampions .= "$tabs<div id='top'></div>\n";
		$graphChampions .= "$tabs<table>\n";
		foreach ($CHAMPIONS as $champion) {
			$championLevel = $champion['championLevel'];
			$chestGranted = $champion['chestGranted'];
			$championId = $champion['championId'];
			$championName = $CHAMPIONS_NAME[$championId];
			$championKey = getChampionKey($championName);
			$highestGrade = isset($champion['highestGrade'])?$champion['highestGrade']:null;
			$championPoints = $champion['championPoints'];
			$nextLevelPoints = $champion['championPointsUntilNextLevel'] + $championPoints;

			$percents = round(100 * $championPoints / $maxpoints, 1);

			$playedChamps[$championId] = $championName;

			$championMasteryScore += $championLevel;
			$totalChampionsPoints += $championPoints;
			if ($championLevel >= 5) { $level5ChampionsPlus += 1; }
			if ($championLevel == 4) { $level4Champions += 1; }


			$graphChampions .= "$tabs	<tr class='level$championLevel".($chestGranted?" chest":"")."'>\n";
			$graphChampions .= "$tabs		<td>\n";
			$graphChampions .= "$tabs			<img src='http://ddragon.leagueoflegends.com/cdn/".API_VERSION."/img/champion/$championKey.png' alt=\"$championName\" title=\"$championName\">\n";
			if (isset($highestGrade)) {
				$graphChampions .= "$tabs			<span class='highestGrade'>$highestGrade</span>\n";
			}
			$graphChampions .= "$tabs		</td>\n";
			$graphChampions .= "$tabs		<td>\n";
			$graphChampions .= "$tabs			<span class='score'>$championPoints</span>\n";
			$graphChampions .= "$tabs			<div title='Level $championLevel: ".formatNumber($championPoints).(($championLevel==5)?"":" / ".formatNumber($nextLevelPoints))." pts' class='bar' style='width: $percents%;'></div>\n";
			$graphChampions .= "$tabs		</td>\n";
			$graphChampions .= "$tabs	</tr>\n";
		}
		$graphChampions .= "$tabs</table>\n";

		// nonPlayedChamps
		$tabs = str_repeat("	", 4);
		$nonPlayedChamps = "";
		foreach ($CHAMPIONS_NAME as $championId => $championName) {
			if (!isset($playedChamps[$championId])) {
				$championKey = getChampionKey($championName);
				$isFree = isset($FREE_TO_PLAY[$championId]);
				$nonPlayedChamps .= "$tabs<img src='http://ddragon.leagueoflegends.com/cdn/".API_VERSION."/img/champion/$championKey.png' alt=\"$championName\" title=\"$championName\"".($isFree?" class='freeToPlay'":"").">\n";
			}
		}

		// echo
?>
			<div id="tabs">
				<ul>
					<li><a href="#masteredChamps">Mastered champions</a></li><!--
					--><li><a href="#otherChamps">Other champions</a></li>
				</ul>
			</div>
			<div id="playedChamps">
				<div id="masteryScores">
					<div>
						<h5>Champions mastery score: <i><?php echo $championMasteryScore; ?> pts</i></h5><!--
						--><h5>Total champions points: <i><?php echo formatNumber($totalChampionsPoints); ?> pts</i></h5><!--
						--><h5>Champions mastered: <i><?php echo $championsPlayed; ?> / <?php echo $numberOfChampions; ?></i></h5>
					</div>
					<div>
						<h5>Level 5+ champions: <i><?php echo $level5ChampionsPlus; ?></i></h5><!--
						--><h5>Level 4 champions: <i><?php echo $level4Champions; ?></i></h5>
					</div>
				</div>
				<div id="scrollToTop">&uArr;</div>
				<div id="graphChampions">
<?php
		echo $graphChampions;
?>
				</div>
			</div>
			<div id='nonPlayedChamps'>
<?php
		if ($nonPlayedChamps == "") {
?>
				<i>This summoner has played all the champions !</i>
<?php
		} else {
		echo $nonPlayedChamps;
?>
				<i>Tip: Free to play champions are yellow bordered. Play them before next tuesday to increase your mastery score !</i>
<?php
		}
?>
			</div>
<?php
	}

	function webetu_request($url) {
		$path = "temp/temp";

		exec("wget \"$url\" -O \"$path\"");

		if (file_exists($path)) {
			$res = file_get_contents($path);
			unlink($path);
			return $res;
		} else {
			return "";
		}
	}

	function api_request($request, $region, $params) {
		global $REGIONS_LIST;

		$summoner_name = isset($params['summoner_name'])?$params['summoner_name']:null;
		$player_id = isset($params['player_id'])?$params['player_id']:null;

		$url = "";
		if (isset($region)) {
			$platformId = $REGIONS_LIST[$region]['id'];
			$region = strtolower($region);

			switch ($request) {
				case 'summoner_by_name':
					if (isset($summoner_name)) {
						$url = "/api/lol/$region/v1.4/summoner/by-name/$summoner_name?";
					}
					break;
			
				case 'champions':
					if (isset($player_id)) {
						$url = "/championmastery/location/$platformId/player/$player_id/champions?";
					}
					break;

				case 'free_to_play':
					$url = "/api/lol/$region/v1.2/champion?freeToPlay=true&";
					break;

				case 'league_entry':
					if (isset($player_id)) {
						$url = "/api/lol/$region/v2.5/league/by-summoner/$player_id/entry?";
					}
					break;
			}
		}

		if ($url != "") {
			$url = "https://$region.api.pvp.net".$url."api_key=".API_KEY;

			if ($_SERVER['SERVER_NAME'] == "webetu.pau.eisti.fr" || $_SERVER['SERVER_NAME'] == "localhost") {
				$res = webetu_request($url);
			} else {
				$res = file_get_contents($url);
			}

			if ($res == "") {
				return null;
			} else {
				return json_decode($res, true);
			}
		}
	}

	function getBestDivision($LEAGUE) {
		function tierToInt($tier) {
			switch ($tier) {
				case "BRONZE": return 1;
				case "SILVER": return 2;
				case "GOLD": return 3;
				case "PLATINUM": return 4;
				case "DIAMOND": return 5;
				case "MASTER": return 6;
				case "CHALLENGER": return 7;
				default: return -1;
			}
		}

		function divToInt($division) {
			switch ($division) {
				case "I": return 1;
				case "II": return 2;
				case "III": return 3;
				case "IV": return 4;
				case "V": return 5;
				default: return -1;
			}
		}

		$best = 0;
		$len = count($LEAGUE);
		for ($i = 1; $i < $len; $i++) {
			if (tierToInt($LEAGUE[$i]['tier']) > tierToInt($LEAGUE[$best]['tier'])) {
				$best = $i;
			} elseif (tierToInt($LEAGUE[$i]['tier']) == tierToInt($LEAGUE[$best]['tier'])) {
				if (divToInt($LEAGUE[$i]['entries'][0]['division'] > divToInt($LEAGUE[$best]['entries'][0]['division']))) {
					$best = $i;
				}
			}
		}

		$tier = ucfirst(strtolower($LEAGUE[$best]['tier']));
		$division = $LEAGUE[$best]['entries'][0]['division'];
		return ['tier' => $tier, 'division' => $division];
	}

?>