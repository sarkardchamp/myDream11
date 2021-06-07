<?php 
require_once 'pdo.php';
session_start();
function SendOTP($value, $otp)
{
	$to = $value['email'];
	$header = "From: <sarkardchamp@gmail.com>";
	$sub = "Reset Password";
	$msg = "Dear ".$value['name'].", OTP to reset your Password at MyDream11 is: ".$otp;
	mail($to, $sub, $msg, $header);
}
if(isset($_POST['search'])) {
	if(isset($_POST['emmob']) && strlen($_POST['emmob']) > 0) {
		$sql = "select * from users where email = '".$_POST['emmob']."' or mobile = '".$_POST['emmob']."';";
		$stmt = $pdo->query($sql);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row) {
			$_SESSION['error'] = "No user found having credential: ".htmlentities($_POST['emmob']);
			header('Location: reset-password.php');
			return;
		} else {
			$_SESSION['found'] = $row;
			$otp = rand(1000, 9999);
			$sql = "update users set otp=:otp where user_id=".$_SESSION['found']['user_id'].";";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':otp' => $otp
			));
			SendOTP($_SESSION['found'],$otp);
			header('Location: reset-password.php');
			return;
		}
	} else {
		$_SESSION['error'] = "Enter Email or Mobile no. to search your Account";
		header('Location: reset-password.php');
		return;
	}
}
if(isset($_POST['verify'])) {
	$sql = "select * from users where user_id=".$_SESSION['found']['user_id'];
	$stmt = $pdo->query($sql);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if(!isset($_POST['otp']) || strlen($_POST['otp'])!=4 || $row['otp']!=$_POST['otp']) {
		$_SESSION['error'] = "Invalid OTP";
		header('Location: reset-password.php');
		return;
	} else if($row['otp']===$_POST['otp']) {
		$_SESSION['otpVerified'] = true;
		header('Location: reset-password.php');
		return;
	}
}
if(isset($_POST['updatePass'])) {
	$sql = "update users set pass=:pass where user_id=".$_SESSION['found']['user_id'].";";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(':pass' => $_POST['password']));
	$_SESSION['success'] = "Password changed SuccessFully";
	header('Location: user-sign-in.php');
	return;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Reset Password</title>
	<?php require_once('bootstrap.php') ?>
</head>
<body>
	<nav id="navbar">
	    <div class="container">
	        <a href="index.php" id="main">MyDream11</a>
	    </div>
	</nav>
	<div class="container mainContent">
		<?php
		if(isset($_SESSION['error'])) {
			echo '<p style="color:#ff0000;">'.$_SESSION['error'].'</p>';
			unset($_SESSION['error']);
		} if(!isset($_SESSION['found'])) { ?>
			<form method="POST">
				<h1>Reset Password</h1>
				<label for="emmob">Email or Mobile no:</label>
				<input type="text" name="emmob" placeholder="your email / mobile no." id="emmob"><br>
				<p><input type="submit" name="search" value="Search Account"> 
					 <a href="index.php">Cancel</a></p>
			</form>
		<?php } else if(isset($_SESSION['found']) && !isset($_SESSION['otpVerified'])) { ?>
			<form method="POST">
				<label for="otp">Enter OTP Received on your Registered Email:</label>
				<input type="text" name="otp" id="otp">
				<p><input type="submit" name="verify" value="Verify OTP"> 
					 <a href="index.php">Cancel</a></p>
			</form>
		<?php } else if(isset($_SESSION['found']) && isset($_SESSION['otpVerified'])) { ?>
			<form method="POST">
				<p id="error" style="color: red;"></p>
				<label for="np">Enter New Password:</label>
				<input type="text" name="newPassword" id="np">
				<label for="rep">Re-enter Password:</label>
				<input type="text" name="password" id="rep">
				<p><input type="submit" name="updatePass" value="Update Password" disabled="disabled" id="up"> 
					 <a href="index.php">Cancel</a></p>
			</form>
		<?php } ?>
	</div>
	<script type="text/javascript">
		var newP = document.getElementById('np');
		var reP = document.getElementById('rep');
		var btn = document.getElementById('up');
		var err = document.getElementById('error')
		newP.addEventListener("focusout",checkLength);
		reP.addEventListener("focusout",matchPassword);
		function checkLength() {
			if(newP.value.length < 4) {
				err.innerHTML = "Password should be atleast 4 characters long";
				btn.setAttribute("disabled","disabled");
				return false;
			} if (reP.value.length > 1) {
				return matchPassword();
			}
			err.innerHTML = "";
			return true;
		}
		function matchPassword() {
			var nptext = newP.value;
			var reptext = reP.value;
			if(nptext != reptext){
				err.innerHTML = "Password mismatch!";
				btn.setAttribute("disabled","disabled");
				return false;
			}
			err.innerHTML = "";
			btn.removeAttribute("disabled");
			return true;
		}
	</script>
</body>
</html>