<?php  
require_once('pdo.php');
session_start();
if(!isset($_SESSION['matchSelected'])) {
    $_SESSION['matchSelected'] = true;
}
if(isset($_SESSION['remPlayers'])) {
    error_log("used the sessions remPlayers");
    echo($_SESSION['remPlayers']);
    return;
}
$players = json_decode(file_get_contents("php://input"),true);
$sql = "select * from players where `Players` not in (";
for($i = 0; $i < count($players); $i++) {
    $sql .= ("'".$players[$i]."'");
    if($i!=count($players)-1) $sql .= ",";
}
$sql .= ") limit ".(22 - count($players)).";";
$stmt = $pdo->query($sql);
$newPlayers = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($newPlayers,$row['Players']);
}
error_log("Did not use sessions remPlayers");
$_SESSION['remPlayers'] = json_encode($newPlayers);
echo($_SESSION['remPlayers']);
return;


/*$id = rand(1,231);
$sql = "select * from files where file_id =".$id;
$stmt = $pdo->query($sql);
$file_name = $stmt->fetch(PDO::FETCH_ASSOC)['file_name'];
$json_file = file_get_contents('./JSON_files/'.$file_name);
$game = json_decode($json_file, true);
$team1_players = array();
$team2_players = array();
$inning1 = $game['innings'][0]['1st innings']['deliveries'];
$inning2 = $game['innings'][1]['2nd innings']['deliveries'];

for($i = 0; $i < count($inning1); $i++) {
    $ball_count = array_keys($inning1[$i])[0];
    $bat = $inning1[$i][$ball_count]['batsman'];
    $ball = $inning1[$i][$ball_count]['bowler'];
    $non_strike = $inning1[$i][$ball_count]['non_striker'];

    $len1 = count($team1_players);
    $exists = false;
    for($j = 0; $j < $len1; $j++) {
        if($team1_players[$j] === $bat) {
            $exists = true;
            break;
        }
    }
    if(!$exists) array_push($team1_players, $bat);
    $len1 = count($team1_players);
    $exists = false;
    for($j = 0; $j < $len1; $j++) {
        if($team1_players[$j] === $non_strike) {
            $exists = true;
            break;
        }
    }
    if(!$exists) array_push($team1_players, $non_strike);


    $len2 = count($team2_players);
    $exists = false;
    for($j = 0; $j < $len2; $j++) {
        if($team2_players[$j] === $ball) {
            $exists = true;
            break;
        }
    }
    if(!$exists) array_push($team2_players, $ball);
}

for($i = 0; $i < count($inning2); $i++) {
    $ball_count = array_keys($inning2[$i])[0];
    $bat = $inning2[$i][$ball_count]['batsman'];
    $ball = $inning2[$i][$ball_count]['bowler'];
    $non_strike = $inning2[$i][$ball_count]['non_striker'];

    $len1 = count($team1_players);
    $exists = false;
    for($j = 0; $j < $len1; $j++) {
        if($team1_players[$j] === $ball) {
            $exists = true;
            break;
        }
    }
    if(!$exists) array_push($team1_players, $ball);


    $len2 = count($team2_players);
    $exists = false;
    for($j = 0; $j < $len2; $j++) {
        if($team2_players[$j] === $non_strike) {
            $exists = true;
            break;
        }
    }
    if(!$exists) array_push($team2_players, $non_strike);


    $len2 = count($team2_players);
    $exists = false;
    for($j = 0; $j < $len2; $j++) {
        if($team2_players[$j] === $bat) {
            $exists = true;
            break;
        }
    }
    if(!$exists) array_push($team2_players, $bat);
}*/
?>