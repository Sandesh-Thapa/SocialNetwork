<?php
require 'config/config.php';



if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
} else {
	header("Location: register.php");
}

?>

<html>

<head>
	<title>Welcome to Socialnet</title>

	<script src="assets/js/jquery.min.js"></script>
	<!-- <script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/bootbox.min.js"></script> -->

	<!-- <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css"> -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo time(); ?>">
	<link rel="stylesheet" type="text/css" href="assets/css/all.min.css">

</head>

<body>
	<header class="header">
		<div class="container">
			<div class="logo">
				<h1>Socialnet</h1>
			</div>
			<div class="searchbar">
				<form method="post">
					<input type="text" name="" id="" placeholder="Search" />
					<button type="submit"><i class="fas fa-search"></i></button>
				</form>
			</div>
			<div class="tools">
				<a href="<?php echo $userLoggedIn; ?>" class="name" title="Profile">
					<img src="<?php echo $user['profile_pic']; ?>">
					<?php echo $user['first_name']; ?>
				</a>
				<a href="index.php" title="Home"> <i class="fas fa-home"></i></a>
				<a href="request.php" title="Friend Request"> <i class="fas fa-user-friends"></i></a>
				<a href="#" title="Messsage"> <i class="fas fa-envelope"></i></a>
				<a href="#" title="Notification"> <i class="fas fa-bell"></i></a>
				<a href="#" title="Settings"> <i class="fas fa-cog"></i></a>
				<a href="includes/handlers/logout.php" title="Log out"> <i class="fas fa-sign-out-alt"></i></a>
			</div>
		</div>
	</header>