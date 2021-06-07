<?php
session_start();
if(isset($_POST['to']) && isset($_POST['from']) && isset($_POST['subject']) && isset($_POST['message'])) {
	if(strlen($_POST['to']) > 0 && strlen($_POST['from']) > 0 && strlen($_POST['subject']) > 0 && strlen($_POST['message']) > 0) {
		$to = $_POST['to'];
		$msg = $_POST['message']."time = ".date('Y/m/d h:m:sa');
		$sub = $_POST['subject'];
		$header = "From:".$_POST['from'];
		$relVal = mail($to,$sub,$msg,$header);
		if($relVal == true) {
			$_SESSION['success'] = "Email sent successfully.";
		} else {
			$_SESSION['error'] = "Email not sent";
		}
	}
	header('Location: mail.php');
	return;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Sample Mail</title>
	<?php require_once('bootstrap.php'); ?>
</head>
<body>
	<div class="container">
		<?php if(isset($_SESSION['error'])) {
			echo '<p style="color:#ff0000;">'.$_SESSION['error'].'</p>';
			unset($_SESSION['error']);
		} else if(isset($_SESSION['success'])) {
			echo '<p style="color:#00ff00;">'.$_SESSION['success'].'</p>';
			unset($_SESSION['success']);
		} ?>
		<h2>Mail System</h2>
		<form method="POST">
			<label>Enter Recipient's Email:<br><input type="email" name="to"></label><br>
			<label>Enter Sender's Email:<br><input type="email" name="from"></label><br>
			<label>Enter Subject of Email:<br><input type="text" name="subject"></label><br>
			<label>Enter Message:<br><textarea name="message" placeholder="Enter your Message here." style="width: 400px; height: 150px;"></textarea></label><br>
			<input type="submit" name="send" value="Send Mail">
		</form>
	</div>
</body>
</html>