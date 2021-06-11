<?php
require_once('pdo.php');
session_start();
if(!isset($_SESSION['registerClicked'])) {
	$_SESSION['registerClicked'] = false;
}

if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['pass'])) {
	error_log('registerClicked');
	if( strlen($_POST['name']) < 1 || strlen($_POST['email']) < 1 || 
		strlen($_POST['mobile']) < 1 || strlen($_POST['pass']) < 1 ) {
		$_SESSION['error'] = "All fields are required";
		header('Location: sign-up.php');
		return;
	} else if (!is_numeric($_POST['mobile']) || strlen($_POST['mobile']) != 10) {
		$_SESSION['error'] = "Mobile should contain 10 digits.";
		header('Location: sign-up.php');
		return;
	} else {
		$otp = rand(1000,9999);
		$sql = "select * from users where email='".$_POST['email']."' or mobile=".$_POST['mobile'];
		$stmt = $pdo->query($sql);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row) {
			$_SESSION['error'] = 'Account already exists for the email/mobile no. entered';
			header('Location: sign-up.php');
			return;
		}
		$sql = "insert into users (name, email, mobile, pass, otp) values (:name, :email, :mobile, :pass, :otp)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
			':name' => $_POST['name'],
			':email' => $_POST['email'],
			':mobile' => $_POST['mobile'],
			':pass' => $_POST['pass'],
			':otp' => $otp
		));
		$msg = "OTP to verify your account is: ".$otp;
		$relVal = mail($_POST['email'], "Account verification", $msg, "From: <sarkardchamp@gmail.com>");
		error_log($relVal);
		$_SESSION['email'] = $_POST['email'];
		$_SESSION['registerClicked'] = true;
		header('Location: sign-up.php');
		return;
	}
}
if(isset($_POST['verifyOtp'])) {
	$stmt = $pdo->query("select * from users where email='".$_SESSION['email']."'");
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$otp = $row['otp'];
	error_log("actual: ".$otp.", user input: ".$_POST['otp']);
	if(htmlentities($otp) == htmlentities($_POST['otp'])) {
		$_SESSION['success'] = "Registration Successful";
		unset($_SESSION['registerClicked']);
		header('Location: index.php');
		return;
	} else {
		$_SESSION['error'] = "Invalid OTP, try again.";
		header('Location: sign-up.php');
		return;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sign Up</title>
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
		if (isset($_SESSION['error'])) {
			echo '<p style="color:red;">'.$_SESSION['error']."</p>";
			unset($_SESSION['error']);
		} 
		if(!$_SESSION['registerClicked']) {?>
			<form method="POST">
				<h1>User Sign Up</h1>
				<label for="name">Name:</label>
				<input type="text" name="name" id="name">
				<label for="email">Email:</label><input type="text" name="email" id="email">
				<label for="mobile">Mobile:</label><input type="text" name="mobile" id="mobile">
				<label for="password">Password:</label><input type="text" name="pass" id="password">
				<p style="margin-top: 10px;"><input type="submit" name="signUp" value="Request OTP">&nbsp;<a href="index.php">Cancel</a></p>
			</form>
		<?php } else { ?> 
			<form method="POST">
				<label>Enter OTP received on your email:<br><input type="text" name="otp" placeholder="OTP..."></label><br>
				<p><input type="submit" name="verifyOtp" value="Verify OTP"> <a href="index.php">Cancel</a></p>
			</form>
		<?php } ?>
	</div>
</body>
</html>