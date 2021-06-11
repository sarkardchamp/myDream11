<?php
require_once 'pdo.php';
session_start();
sleep(1);
if(!isset($_SESSION['playerDataSet'])) {
	$_SESSION['playerDataSet'] = true;
}
$players = json_decode(file_get_contents("php://input"),true);
$sql = "select * from players where `Players` in (";
for ($i=0; $i < 22; $i++) { 
	$sql .= ("'".$players[$i]."'");
	if($i < 21) $sql .= ",";
}
$sql .= ");";
$stmt = $pdo->query($sql);
$playersData = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	array_push($playersData, $row);
}
$playersData = json_encode($playersData);
echo($playersData);

?>