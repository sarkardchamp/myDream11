<?php
require_once('pdo.php');
session_start();
if(isset($_SESSION['id'])) {
	header('Location: dashboard.php');
	return;
}
if(isset($_POST['login'])) {
	if(!isset($_POST['emmob']) || !isset($_POST['pass']) || strlen($_POST['emmob']) < 1 || strlen($_POST['pass']) < 1) {
		$_SESSION['error'] = 'Email / Mobile no. and Password are required.';
		header("Location: user-sign-in.php");
		return;
	}
	else {
		$sql = "select * from users where email = '".strtolower($_POST['emmob'])."' or mobile = '".$_POST['emmob']."'";
		$stmt = $pdo->query($sql);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row) {
			$_SESSION['error'] = 'No user found with Email / Mobile no: '.$_POST['emmob'];
			header('Location: user-sign-in.php');
			return;
		} else if($_POST['pass'] !== $row['pass']) {
			$_SESSION['error'] = 'Incorrect Password';
			header('Location: user-sign-in.php');
			return;
		} else if ($_POST['pass'] === $row['pass']) {
			$_SESSION['success'] = 'Login successful.';
			$_SESSION['id'] = $row['user_id'];
			header('Location: dashboard.php');
			return;
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php require_once('bootstrap.php') ?>
	<title>Sign-in / Sign-up </title>
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
		} else if (isset($_SESSION['success'])) {
			echo '<p style="color:#0f0">'.$_SESSION['success'].'</p>';
			unset($_SESSION['success']);
		}
		?>
		<form method="POST">
			<h1>User Sign in</h1>
			<label for="emmob">Email or Mobile no:</label>
			<input type="text" name="emmob" placeholder="your email / mob no." id="emmob"><br>
			<label for="password">Password:</label>
			<input type="password" name="pass" id="password">
			<p style="margin-top: 10px;"><input type="submit" name="login" value="Login">&nbsp;or <a href="index.php">Cancel</a></p>
			<br><br>
			<p>Forgot Password? <a href="reset-password.php">Reset here</a></p>
			<p>New to My Dream11? <a href="sign-up.php">create a new account.</a></p>
		</form>
	</div>
</body>
</html>