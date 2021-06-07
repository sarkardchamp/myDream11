<?php
require_once('pdo.php');
session_start();
if(!isset($_SESSION['id'])) {
	die("Unauthorised! Access denied. Please ".'<a href="user-sign-in.php">Sign in</a> to continue.');
} else {
	$stmt = $pdo->query("select * from users where user_id = ".$_SESSION['id']);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php require_once('bootstrap.php') ?>
	<title>Welcome to My Dream11</title>
</head>
<body>
	<nav id="navbar">
	    <div class="container">
	        <a href="index.php" id="main">MyDream11</a>
	        <div class="dropdown">
	            <a id="profile">Profile <span class="glyphicon glyphicon-chevron-down"></span></a>
	            <div id="userProfile">
	            	<a href="#">View Profile</a>
	            	<a href="logout.php">Sign Out</a>
	            </div>
	        </div>
	    </div>
	</nav>
	<div class="container mainContent">
		<?php if(isset($_SESSION['success'])) {
			// echo '<p style="color:#00ff00;">'.$_SESSION['success']."</p>";
			unset($_SESSION['success']);
		} ?>
		<h1>Hello <?php echo $row['name']; ?><br>Welcome to My Dream11</h1>
		<button id="matchSelection" >Select a Random Match</button>
		<button id="player_display">View Players</button>
		<button id="match_display">Begin Match</button>
		<div id="credit">
			Credits: <span id="creVal">100</span>
		</div>
		<div id="match">

		</div>
	</div>
	<script type="text/javascript">
		document.getElementById('matchSelection').addEventListener("click", selectMatch);
		document.getElementById('player_display').addEventListener("click", preparePlayerData);
		var matchJSON = "";
		var mPlayers = [];
		var mTeams = [];
		var mPlace = "";
		var mType = "";
		var mPlayerData;
		var xhttp = new XMLHttpRequest();
		var target = document.getElementById('match');
		var mCustomTeam = [];

		function selectMatch() {
			if(matchJSON.length < 1) {
				xhttp.onreadystatechange = function (data) {
					if(this.readyState == 4 && this.status == 200) {
						matchJSON = this.responseText;
						prepareMatch(matchJSON);
						document.getElementById('player_display').style.visibility = 'visible';
						document.getElementById('matchSelection').style.display = 'none';
					}
				}
				xhttp.open("GET","select_game.php",true);
				xhttp.send();
			}
		}
		function prepareMatch(matchJSON) {
			var matchObj = JSON.parse(matchJSON);
			mTeams = matchObj["info"]["teams"];
			mType = matchObj["info"]["competition"] + " " + matchObj["info"]["match_type"];
			mPlace = matchObj["info"]["venue"] + ", " + matchObj["info"]["city"];
			preparePlayerList(matchObj);
			renderMatch();
		}
		function preparePlayerList(matchObj) {
			var inning1, inning2, batsman, bowler, nonStriker, delivery, key, bt;
			inning1 = matchObj["innings"][0]["1st innings"]["deliveries"];
			inning2 = matchObj["innings"][1]["2nd innings"]["deliveries"];
			for(let i = 0; i < inning1.length; i++) {
				delivery = inning1[i];
				key = Object.keys(delivery);
				batsman = delivery[key[0]]["batsman"];
				bowler = delivery[key[0]]["bowler"];
				nonStriker = delivery[key[0]]["non_striker"];
				bt = false;
				for(let j = 0; j < mPlayers.length; j++) {
					if(mPlayers[j] == batsman) {
						bt = true;
						break;
					}
				}
				if(!bt) mPlayers.push(batsman);
				bt = false;
				for(let j = 0; j < mPlayers.length; j++) {
					if(mPlayers[j] == bowler) {
						bt = true;
						break;
					}
				}
				if(!bt) mPlayers.push(bowler);
				bt = false;
				for(let j = 0; j < mPlayers.length; j++) {
					if(mPlayers[j] == nonStriker) {
						bt = true;
						break;
					}
				}
				if(!bt) mPlayers.push(nonStriker);

			}
			for(let i = 0; i < inning2.length; i++) {
				delivery = inning2[i];
				key = Object.keys(delivery);
				batsman = delivery[key[0]]["batsman"];
				bowler = delivery[key[0]]["bowler"];
				nonStriker = delivery[key[0]]["non_striker"];
				bt = false;
				for(let j = 0; j < mPlayers.length; j++) {
					if(mPlayers[j] == batsman) {
						bt = true;
						break;
					}
				}
				if(!bt) mPlayers.push(batsman);
				bt = false;
				for(let j = 0; j < mPlayers.length; j++) {
					if(mPlayers[j] == bowler) {
						bt = true;
						break;
					}
				}
				if(!bt) mPlayers.push(bowler);
				bt = false;
				for(let j = 0; j < mPlayers.length; j++) {
					if(mPlayers[j] == nonStriker) {
						bt = true;
						break;
					}
				}
				if(!bt) mPlayers.push(nonStriker);

			}
			var playerJSON = JSON.stringify(mPlayers);
			xhttp.onreadystatechange = function () {
				if(this.readyState == 4 && this.status == 200) {
					var remPlayers = JSON.parse(this.responseText);
					for (var i = 0; i < remPlayers.length; i++) {
						mPlayers.push(remPlayers[i]);
					}
					console.log("players list:\n" + mPlayers + "\n num of players: " + mPlayers.length);
				}
			}
			xhttp.open("POST","select_players.php",true);
			xhttp.setRequestHeader("Content-type","application/json");
			xhttp.send(playerJSON);

		}
		function renderMatch() {
			var heading = document.createElement('h3');
			var venue = document.createElement('p');
			var matchType = document.createElement('div');
			var matchTypeP = document.createElement('p');
			var imgMatch = document.createElement('img');
			var node = document.createElement('div');
			var team1 = document.createElement('div');
			var team2 = document.createElement('div');
			var vs = document.createElement('div');
			var img1 = document.createElement('img');
			var img2 = document.createElement('img');
			var para1 = document.createElement('h4');
			var para2 = document.createElement('h4');
			var playerTable = document.createElement('table');


			heading.innerHTML = "Match Details";
			venue.innerHTML = "Venue: " + mPlace;
			matchTypeP.innerHTML = "Match type: " + mType;
			imgMatch.alt = mType + " logo";
			imgMatch.src = "images/IPL.png";
			imgMatch.style.height = "70px";
			imgMatch.style.marginBottom = "20px";
			matchType.appendChild(matchTypeP);
			matchType.appendChild(imgMatch);
			node.setAttribute("class","teams");
			playerTable.id = "players";
			img1.alt = mTeams[0] + ' logo';
			img2.alt = mTeams[1] + ' logo';
			img1.src = "images/" + mTeams[0] + ".png";
			img2.src = "images/" + mTeams[1] + ".png";
			vs.innerHTML = 'V/s';
			vs.setAttribute("class","vs");
			para1.innerHTML = mTeams[0];
			para2.innerHTML = mTeams[1];

			team1.appendChild(para1);
			team1.appendChild(img1);
			team2.appendChild(para2);
			team2.appendChild(img2);

			node.appendChild(team1);
			node.appendChild(vs);
			node.appendChild(team2);

			target.appendChild(heading);
			target.appendChild(matchType);
			target.appendChild(venue);
			target.appendChild(node);
			target.appendChild(playerTable);
		}
		function preparePlayerData() {
			playerJSON = JSON.stringify(mPlayers);
			var xhttpPdata = new XMLHttpRequest();
			xhttpPdata.onreadystatechange = function() {
				if(this.readyState == 4 && this.status == 200) {
					// console.log("Player's Data:\n" + this.responseText);
					mPlayerData = JSON.parse(this.responseText);
					document.getElementById('credit').style.display = "block";
					displayPlayers();
				}
			}
			xhttpPdata.open("POST", "get-players-data.php", true);
			xhttpPdata.setRequestHeader("Content-type","application/json");
			xhttpPdata.send(playerJSON);
		}
		function displayPlayers() {
			document.getElementById('player_display').style.display = "none";
			document.getElementById('match_display').style.visibility = "visible";
			var tbl = document.getElementById('players');
			tbl.setAttribute("class","tbl");
			tbl.setAttribute("id","tbl");
			var txt = '<thead><th>S. no.</th><th>Player Name</th><th colspan="2">Credit Cost</th></thead><tbody>';
			for (var i = 0; i < mPlayerData.length; i++) {
				var player = mPlayerData[i];
				// console.log(player);
				txt += '<tr onclick="addToTeam(this);"><td>' + (i+1) + '</td><td>' + player["Players"] + '</td><td>' + player["Credit Value"] + '</td><td>' + '+' + '</td></tr>';
			}
			txt += '</tbody>';
			tbl.innerHTML = txt;

			var teamTable = document.createElement('table');
			var teamDiv = document.createElement('div');
			var teamCnf = document.createElement('button');
			teamDiv.id = "teamTable";
			teamCnf.id = "team_confirmation";
			teamCnf.disabled = "disabled";
			teamCnf.class = "disabled";
			teamCnf.innerHTML = 'Confirm Team';
			teamTable.innerHTML = '';

			teamDiv.appendChild(teamTable);
			teamDiv.appendChild(teamCnf);
			target.appendChild(teamDiv);

		}
		function addToTeam(row) {
			var rows = document.getElementById('tbl').getElementsByTagName('tr');
			var creditVal = document.getElementById('creVal');

			var teamDiv = document.getElementById('teamTable');
			var tbl = teamDiv.getElementsByTagName('table')[0];

			var trData = row.getElementsByTagName('td');
			var pData = {name:trData[1].innerText,credit:Number(trData[2].innerText)};

			if(trData[3].innerText == '+' && creditVal.innerText-pData.credit>=0 && mCustomTeam.length < 11) {
				mCustomTeam.push(pData);
				row.setAttribute("class","disabled");
				trData[3].innerText = '-';
				row.setAttribute("title","Click to de-select");
				creditVal.innerText = (Number(creditVal.innerText) - pData.credit).toFixed(1);
			} else if(trData[3].innerText == '-') {
				var idx = 0;
				while(mCustomTeam[idx].name != pData.name) idx++;
				for(;idx < mCustomTeam.length-1; idx++) {
					mCustomTeam[idx] = mCustomTeam[idx+1];
				}
				mCustomTeam.pop();
				row.removeAttribute("class");
				trData[3].innerText = '+';
				row.removeAttribute("title");
				creditVal.innerText = (Number(creditVal.innerText) + pData.credit).toFixed(1);
			}
			var txt = '<thead><tr><th>Player</th><th>Credit</th></tr></thead><tbody>';
			for(let i = 0; i < mCustomTeam.length; i++) {
				txt += ('<tr><td>' + mCustomTeam[i].name + '</td><td>' + mCustomTeam[i].credit + '</td></tr>');
			}
			txt += '</tbody>';
			console.log(txt);
			tbl.innerHTML = txt;
			if(mCustomTeam.length == 0) {
				teamDiv.style.display = "none";
			} else if(mCustomTeam.length <= 11) {
				teamDiv.style.display = "block";
			}
			var btn = document.getElementById("team_confirmation");
			if(mCustomTeam.length == 11) {
				btn.disabled = "disabled";
				btn.removeAttribute("disabled");
				btn.removeAttribute("class");
			} else {
				btn.setAttribute("class","disabled");
			}


			for(let i = 1; i < rows.length; i++) {
				var rowData = rows[i];
				var tds = rowData.getElementsByTagName('td');
				if(tds[2].innerHTML-creditVal.innerHTML > 0) {
					rows[i].setAttribute("class","disabled");
				}
				else if(tds[3].innerText == '+') {
					rows[i].setAttribute("class"," ");
					rows[i].removeAttribute("class");
				}
			}

		}

	</script>
</body>
</html>