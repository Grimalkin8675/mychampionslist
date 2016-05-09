<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">

	<title>Margin Champions</title>

	<style type="text/css">
		@font-face {
			font-family: fontLol;
			src: url("../css/FrizQuadrataRegular.woff");
		}
		body {
			font-size: 12pt;
			font-family: fontLol;
		}
		input {
			font-family: fontLol;
		}
		p {
			width: 1002px;
			margin: auto;
			font-weight: bold;
		}
		.crop {
			margin: 0px auto 30px;
			height: 124px;
			width: 1002px;
			overflow: hidden;
		}
		.crop img {
			width: 1002px;
			height: 592px;
		}
		p#resultat {
			font-weight: normal;
			word-break: break-all;
			border-bottom: 3px double black;
			border-top: 3px double black;
			margin: 1em auto;
		}
	</style>

	<script type="text/javascript">
		var HAUTEUR_IMG = 592;
		var HAUTEUR_DIV = 124;
		var UN_POURCENT = HAUTEUR_IMG / 100;
		var eltDeplace;
		var coordYInit;
		var marginTopInit;

		// 5.92px = 1%
		// 1px = 1/5.92%

		function initDepl(event) {
			eltDeplace = event.target;
			coordYInit = event.screenY;
			marginTopInit = eltDeplace.style.marginTop==""?0:Math.round(parseFloat(eltDeplace.style.marginTop)*UN_POURCENT);
		}

		function deplImg(event) {
			var valDepl = marginTopInit + event.screenY - coordYInit;

			if (valDepl > 0) {
				valDepl = 0;
			} else if (valDepl < (HAUTEUR_DIV - HAUTEUR_IMG)) {
				valDepl = HAUTEUR_DIV - HAUTEUR_IMG;
			}

			eltDeplace.style.marginTop = Math.round(valDepl/UN_POURCENT*10)/10+"%";
		}

		function getChampionName(name) {
			switch (name) {
				case "Cho'Gath": return "Chogath";
				case "Kha'Zix": return "Khazix";
				case "Vel'Koz": return "Velkoz";

				case "Fiddlesticks": return "FiddleSticks";
				case "LeBlanc": return "Leblanc";
				case "Wukong": return "MonkeyKing";

				default: return name.replace(/ |\.|'/g, "");
			}
		}

		function requeteJSON(fichier, fonction) {
			var xhttp;
			if (window.XMLHttpRequest) {
				xhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}

			xhttp.onreadystatechange = function() {
				if (xhttp.readyState == 4 && xhttp.status == 200) {
					fonction(JSON.parse(xhttp.responseText));
				}
			};
			xhttp.open("GET", "http://ritopls.blbl.moe/static/json/"+fichier, true);
			xhttp.send();
		}

		function init() {
			var divContent = document.getElementById('content');

			requeteJSON("champions_name.json", function (NOMS_CHAMP) {
				requeteJSON("champions_margin.json", function (MARGIN_TOP) {
					for (var id in NOMS_CHAMP) {
						var divCrop = document.createElement('div');
						var pNomChamp = document.createElement('p');
						var imgChamp = document.createElement('img');

						var nomChamp = getChampionName(NOMS_CHAMP[id]);

						if (MARGIN_TOP[nomChamp] === undefined) {
							MARGIN_TOP[nomChamp] = 0;
						}

						divCrop.className = "crop";

						pNomChamp.innerHTML = "id: "+id+" - "+NOMS_CHAMP[id];

						imgChamp.src = "http://ddragon.leagueoflegends.com/cdn/img/champion/splash/"+nomChamp+"_0.jpg";
						imgChamp.draggable = "true";
						imgChamp.style.marginTop = MARGIN_TOP[nomChamp]+"%";

						imgChamp.ondragstart = function (event) { initDepl(event) };
						imgChamp.ondrag = function (event) { deplImg(event) };
						imgChamp.ondragend = function (event) { deplImg(event) };

						divCrop.appendChild(imgChamp);
						divContent.appendChild(pNomChamp);
						divContent.appendChild(divCrop);
					}
				});
			});
		}

		function exportDonnees() {
			var res = {};

			for (var i = 0; i < document.images.length; i++) {
				var margTop = document.images[i].style.marginTop;
				margTop = margTop==""?0:Math.round(parseFloat(margTop)*10)/10;

				var nomChamp = document.images[i].src;
				nomChamp = nomChamp.slice(59, nomChamp.indexOf("_"));

				res[nomChamp] = margTop;
			}

			var sortie = JSON.stringify(res);
			console.log(res);
			document.getElementById("resultat").innerHTML = sortie;
		}
	</script>
</head>
<body onload="init();">

 	<input type="button" value="Export JSON" onclick="exportDonnees();">
 	<input type="button" value="Erase" onclick="document.getElementById('resultat').innerHTML  = '';">
 	<p id="resultat"></p>

 	<div id="content">
 	</div>

</body>
</html>