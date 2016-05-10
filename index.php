<?php

	include "static/config.php";
	include "static/static.php";
	include ".api_key.php";

	$REGION = isset($_GET['region'])?strtoupper(check_input($_GET['region'])):"NA";
	if (isset($_GET['pseudo'])) {
		$PSEUDO = check_input($_GET['pseudo']);
	} else {
		$PSEUDO = null;
	}

	if (isset($REGIONS_LIST[$REGION])) {
		if (isset($PSEUDO)) {
			$pseudo_lower = str_replace(" ", "", strtolower($PSEUDO));

			$SUMMONER = api_request("summoner_by_name", $REGION, ["summoner_name" => $pseudo_lower]);

			if (isset($SUMMONER)) {
				$SUMMONER = $SUMMONER[$pseudo_lower];
				$PSEUDO = $SUMMONER['name'];

				$CHAMPIONS = api_request("champions", $REGION, ["player_id" => $SUMMONER['id']]);

				if (isset($CHAMPIONS)) {
					if ($CHAMPIONS == []) {
						$CHAMPIONS = null;
						$error = "This summoner has no mastery points.";
					} else {
						$CHAMPIONS_NAME = json_decode(file_get_contents("json/champions_name.json"), true);
						$CHAMPIONS_MARGIN = json_decode(file_get_contents("json/champions_margin.json"), true);

						$FREE_TO_PLAY = api_request("free_to_play", $REGION, []);

						if (isset($FREE_TO_PLAY)) {
							$res = [];
							foreach ($FREE_TO_PLAY['champions'] as $champion) {
								$id = $champion['id'];
								$res[$id] = $CHAMPIONS_NAME[$id];
							}
							$FREE_TO_PLAY = $res;
						}
					}
				} else {
					$error = "This summoner has no mastery points.";
				}

				$LEAGUE = api_request("league_entry", $REGION, ["player_id" => $SUMMONER['id']]);
				if (isset($LEAGUE)) {
					$LEAGUE = getBestDivision($LEAGUE[$SUMMONER['id']]);
				}
			} else {
				$error = "Summoner not found.";
			}
		}
	} else {
		$error = "Invalid region name.";
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php
		if (isset($PSEUDO)) {
			echo $PSEUDO;
		} else {
			echo "My Champions List";
		}
	?></title>

	<link href="<?php echo APP_ROOT; ?>/img/icons/icon2.png" rel="shortcut icon" type="image/png">

	<meta charset="UTF-8">

	<link rel="stylesheet" type="text/css" href="<?php echo APP_ROOT; ?>/css/style.css">
	<script type="text/javascript" src="<?php echo APP_ROOT; ?>/js/script.js"></script>

</head>
<body>

	<div id="content">

		<!-- Title and search bar -->
		<header>
			<a href="<?php echo APP_ROOT; ?>" id="home">My Champions List</a>
			<!-- <h4>Search a summoner to see how many mastery points he has with the champions he played, the highest grades he got, and much more.</h4> -->
			<span id="searchBar">
				<form>
					<input id="summoner_name" type="text" name="pseudo" value="<?php echo isset($PSEUDO)?"$PSEUDO":""; ?>" placeholder="Find summoner" autofocus><!--
					--><select id="region" name="region">
<?php
					foreach ($REGIONS_LIST as $id => $region) {
?>
						<option value="<?php echo $id; ?>" <?php if ($id==$REGION) echo " selected"; ?>><?php echo $id; ?></option>
<?php
					}
?>
					</select>
				</form>
				<a href="<?php
						echo APP_ROOT;
						if (isset($PSEUDO)) {
							switch ($_SERVER['SERVER_NAME']) {
								case "webetu.pau.eisti.fr":
								case "localhost":
									echo "?region=$REGION&pseudo=$PSEUDO";
									break;
								default:
									echo "$REGION/$PSEUDO";
									break;
							}
						}
					?>" id="search">Search</a>
			</span>		
		</header>


<?php
		if (isset($SUMMONER)) {
?>
		<!-- Summoner's informations -->
		<div id="summoner">
			<img src=<?php echo '"http://ddragon.leagueoflegends.com/cdn/'.API_VERSION.'/img/profileicon/'.$SUMMONER['profileIconId'].'.png" alt="icon '.$SUMMONER['profileIconId'].'"'; ?> id="icon">
			<div id="summInfos">
				<h3><?php echo $PSEUDO; ?></h3>
				<h2><?php
					if (isset($LEAGUE)) {
						echo $LEAGUE['tier'];
						if ($LEAGUE['tier'] != "Master" && $LEAGUE['tier'] != "Challenger") {
							echo " ",$LEAGUE['division'];
						}
					} else {
						echo "Level ",$SUMMONER['summonerLevel'];
					}
				?></h2>
			</div>
<?php
			if (isset($CHAMPIONS)) {
				$best_champ = $CHAMPIONS[0];
?>
			<div id='championsPoints'><?php echo round($best_champ['championPoints']/1000); ?>k</div>
<?php
			}
?>
			<div id="bg-banner">
<?php
				if (isset($CHAMPIONS)) {
					$championName = $CHAMPIONS_NAME[$best_champ['championId']];
					$championKey = getChampionKey($championName);

					$championMargin = $CHAMPIONS_MARGIN[$championKey];
?>
				<img src="http://ddragon.leagueoflegends.com/cdn/img/champion/splash/<?php echo $championKey; ?>_0.jpg" alt="$championName" style="margin-top: <?php echo $championMargin; ?>%;">
<?php
				} else {
?>
				<img src="<?php echo APP_ROOT; ?>/img/banners/banner.png" alt="Old Bearded">
<?php
				}
?>
			</div>
		</div>
<?php
		}
		if (isset($error)) {
?>
		<div id="error">
			<?php echo $error; ?>
		</div>
<?php
		}
?>


<?php
		if (isset($CHAMPIONS)) {
?>
		<!-- Champions informations -->
		<div id="champions">
<?php
			display_champions($CHAMPIONS);
		}
?>
		</div>
	</div>


	<!-- Scripts -->
	<script type="text/javascript">
		init_js();
	</script>

</body>
</html>
