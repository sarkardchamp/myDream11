<?php  
require_once('pdo.php');
session_start();
if(isset($_SESSION['id'])) {
    header('Location: dashboard.php');
    return;
}
if(isset($_SESSION['registerClicked']))
    unset($_SESSION['registerClicked']);
session_destroy();
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <?php require_once('bootstrap.php') ?>
    <title>My dream11</title>
</head>
<body>
<nav id="navbar">
    <div class="container">
        <a href="index.php" id="main">MyDream11</a>
        <div id="signIn">
            <a href="user-sign-in.php" type="button">Sign In</a>
            <a href="sign-up.php">Sign Up</a>
        </div>
    </div>
</nav>
<div class="container mainContent">
    <?php if(isset($_SESSION['success'])) {
        echo '<p style="color:#00ff00;">'.$_SESSION['success'].'</p>';
        unset($_SESSION['success']);
    } ?>
    <h2>Hi User!</h2>
    <h3>Welcome to My Dream11</h3>
    <p>It is so nice to have you here. Please <a href="user-sign-in.php">Sign in</a> to continue.</p>
    <p>New to My Dream11? It's really fun. <a href="sign-up.php">Sign Up Now!</a></p>
    <div style="height: 800px; width: 100%;"></div>
</div>
</body>
</html>