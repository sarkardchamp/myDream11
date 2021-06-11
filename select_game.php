<?php  
require_once('pdo.php');
session_start();
sleep(1);
if(!isset($_SESSION['match_id'])) {
	$_SESSION['match_id'] = rand(1,231);
}
$id = $_SESSION['match_id'];
$sql = "select * from files where file_id =".$id;
$stmt = $pdo->query($sql);
$file_name = $stmt->fetch(PDO::FETCH_ASSOC)['file_name'];
$json_file = file_get_contents('./JSON_files/'.$file_name);
echo($json_file);
// $game = json_decode($json_file, true);
// $match_info = $game['info'];
// echo(json_encode($match_info));
// $inning1 = $game['innings'][0]['1st innings']['deliveries'];
// $inning2 = $game['innings'][1]['2nd innings']['deliveries'];
