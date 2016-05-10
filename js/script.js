function resize_page() {
	var bg_banner = document.getElementById('bg-banner');

	if (bg_banner != undefined) {
		bg_banner.style.height = Math.round(bg_banner.clientWidth * 128 / 1006) + "px";
	}
}

function change_link() {
	var host = window.location.hostname;
	var webetu = (host == "webetu.pau.eisti.fr" || host == "webetu.eistiens.work");

	var summoner_name = document.getElementById('summoner_name').value.replace(/ /g, "%20");
	var region = document.getElementById('region').value.toUpperCase();
	var hash = window.location.hash;

	var link = document.getElementById('search');
	
	if (webetu) {
		link.href = window.location.origin+window.location.pathname+"?region="+region+"&pseudo="+summoner_name/*+hash*/;
	} else {
		if (summoner_name == "") {
			link.href = window.location.origin+"/"+region/*+hash*/;
		} else {
			link.href = window.location.origin+"/"+region+"/"+summoner_name/*+hash*/;
		}
	}
}

function search_summoner() {
	var host = window.location.hostname;
	var webetu = (host == "webetu.pau.eisti.fr" || host == "webetu.eistiens.work");

	var summoner_name = document.getElementById('summoner_name').value.replace(/ /g, "%20");
	var region = document.getElementsByTagName('select')[0].value
	var hash = window.location.hash;

	if (webetu) {
		window.open(window.location.origin+window.location.pathname+"?region="+region+"&pseudo="+summoner_name/*+hash*/, "_self");
	} else {
		if (summoner_name == "") {
			window.open(window.location.origin+"/"+region/*+hash*/, "_self");
		} else {
			window.open(window.location.origin+"/"+region+"/"+summoner_name/*+hash*/, "_self");
		}
	}
	
	return false;
}

function show_tab(played) {
	document.getElementById('playedChamps').className = played?"":"hidden";
	document.getElementById('nonPlayedChamps').className = played?"hidden":"";

	document.getElementById('tabs').getElementsByTagName('a')[0].className = played?"active":"";
	document.getElementById('tabs').getElementsByTagName('a')[1].className = played?"":"active";
}

function scrollToTop(delay) {
	var scrollTop = document.body.scrollTop; // px
	var totalScrollDelay = 500; // ms
	var step = 25; // px
	delay = delay==undefined?((scrollTop==0)?0:parseInt(totalScrollDelay*step/scrollTop)):delay; // ms

	scrollTop -= step;

	if (scrollTop < 0) {
		document.body.scrollTop = 0;
	} else {
		document.body.scrollTop = scrollTop;
		setTimeout(function () { scrollToTop(delay) }, delay);
	}
}

function bindOnScrollEvent() {
	document.body.onscroll = function () {
		console.log("*scroll*");
		var masteryScoresElt = document.getElementById('masteryScores');
		var offsetTop = document.getElementById('playedChamps').offsetTop;

		if (document.body.scrollTop > offsetTop) {
			masteryScoresElt.className = "fixed";
		} else {
			masteryScoresElt.className = "";
		}
	};
}

function init_js() {
	document.getElementById('search').onfocus = function () { change_link(); }

	document.getElementsByTagName('form')[0].onsubmit = function () { search_summoner(); }

	resize_page();
	document.body.onresize = function () {
		resize_page();
	};
	
	var tabs = document.getElementById('tabs');
	if (tabs != undefined) {
		show_tab(window.location.hash!="#otherChamps");
		var anchors = tabs.getElementsByTagName('a');
		anchors[0].onclick = function () { show_tab(true); }
		anchors[1].onclick = function () { show_tab(false); }
	}

	var scrollToTopElt = document.getElementById('scrollToTop');
	if (scrollToTopElt != undefined) {
		scrollToTopElt.onclick = function () { scrollToTop(); };
	}

	bindOnScrollEvent();
}
