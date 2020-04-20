<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
?>



<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="assets/css/register_style.css?v=<?php echo time(); ?>" />
	<link rel=" stylesheet" href="assets/css/all.min.css" />
	<title>Socialnet</title>
</head>

<body>
	<header>
		<div class="container">
			<h1 class="logo">Socialnet</h1>
			<div class="login">
				<form action="register.php" method="POST">
					<input type="email" name="log_email" placeholder="Email..." value=" <?php
																						if (isset($_SESSION['log_email'])) {
																							echo $_SESSION['log_email'];
																						} ?>" required />
					<input type="password" name="log_password" id="" placeholder="Password..." required />
					<input type="submit" name="login_button" value="Log In" />
					<br />
				</form>
				<?php if (in_array("Email or password was incorrect<br>", $error_array)) echo "Email or password was incorrect<br>" ?>
			</div>
		</div>
	</header>
	<main>
		<div class="overlay"></div>
		<div class="showcase">
			<div class="container">
				<div class="show">
					<h2>Welcome to <span style="color: #dd123d;">Socialnet</span></h2>

					<div class="footnote">
						<h4>
							<i class="fas fa-newspaper"></i>&nbsp;&nbsp;See photos and
							updates from friends in News Feed
						</h4>
						<h4>
							<i class="fas fa-share-square"></i>&nbsp;&nbsp;Share what's new
							in your life on your Timeline.
						</h4>
						<h4>
							<i class="fas fa-search"></i>&nbsp;&nbsp;Find more of what
							you're looking for with Socialnet Search.
						</h4>
					</div>
				</div>
				<div class="register">
					<div class="form">
						<h4><span>Sign up</span> now</h4>
						<form action="register.php" method="POST">
							<input type="text" name="reg_fname" placeholder="First Name" required value=" <?php
																											if (isset($_SESSION['reg_fname'])) {
																												echo $_SESSION['reg_fname'];
																											} ?>" />
							<br />
							<?php if (in_array("Your first name must be between 2 and 25 characters<br>", $error_array)) echo "Your first name must be between 2 and 25 characters<br>"; ?>
							<input type="text" name="reg_lname" placeholder="Last Name" required value=" <?php
																											if (isset($_SESSION['reg_lname'])) {
																												echo $_SESSION['reg_lname'];
																											} ?>" />
							<br />
							<?php if (in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>" ?>
							<input type="email" name="reg_email" placeholder="Email" required value=" <?php
																										if (isset($_SESSION['reg_email'])) {
																											echo $_SESSION['reg_email'];
																										} ?>" />
							<br />

							<input type="email" name="reg_email2" placeholder="Confirm Email" required value=" <?php
																												if (isset($_SESSION['reg_email2'])) {
																													echo $_SESSION['reg_email2'];
																												} ?>" />
							<br />

							<?php
							if (in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>";
							else if (in_array("Invalid Email Format<br>", $error_array)) echo "Invalid Email Format<br>";
							else if (in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
							?>

							<input type="password" name="reg_password" placeholder="Password" required />
							<br />
							<input type="password" name="reg_password2" placeholder="Confirm Password" required />
							<br />

							<?php
							if (in_array("Password don't match<br>", $error_array)) echo "Password don't match<br>";
							else if (in_array("Your password must be between 5 and 30 characters<br>", $error_array)) echo "Your password must be between 5 and 30 characters<br>";
							?>

							<input type="submit" name="register_button" value="Sign Up" />
							<br>

							<?php if (in_array("<span style='color: green;'>You're ready to go! Go ahead and login!</span>", $error_array)) echo "<span style='color: green;'>You're ready to go! Go ahead and login!</span>"; ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	</main>
</body>

</html>