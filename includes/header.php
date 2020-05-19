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
	<script src="assets/js/main.js"></script>

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
				<form action="search.php" method="GET" name="search_form">
					<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn ?>')" name="q" placeholder="Search" autocomplete="off" id="searchTextInput" />
					<button><i class="fas fa-search"></i></button>
				</form>
			</div>
			<div class="tools">
				<a href="<?php echo $userLoggedIn; ?>" class="name" title="Profile">
					<img src="<?php echo $user['profile_pic']; ?>">
					<?php echo $user['first_name']; ?>
				</a>
				<a href="index.php" title="Home"> <i class="fas fa-home"></i></a>
				<a href="request.php" title="Friend Request"> <i class="fas fa-user-friends"></i></a>
				<a href="javascript:void(0);" title="Messsage" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')"> <i class="fas fa-envelope"></i></a>
				<a href="#" title="Notification"> <i class="fas fa-bell"></i></a>
				<a href="settings.php" title="Settings"> <i class="fas fa-cog"></i></a>
				<a href="includes/handlers/logout.php" title="Log out"> <i class="fas fa-sign-out-alt"></i></a>
			</div>
		</div>
	</header>
	<div class="dropdown-data-window" style="height:0px;"></div>
	<input type="hidden" id="dropdown_data_type" value="">

	<script>
		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
		$(document).ready(function() {

			$('.dropdown-data-window').scroll(function() {
				var inner_height = $('.dropdown-data-window').innerHeight(); //Div containing messages
				var scroll_top = $('.dropdown-data-window').scrollTop();
				var page = $('.dropdown-data-window').find('.nextPageDropdownData').val();
				var noMoreData = $('.dropdown-data-window').find('.noMoreDropdownData').val();

				if ((scroll_top + inner_height >= $('.dropdown-data-window')[0].scrollHeight) && noMoreData == 'false') {

					var pageName;
					var type = $('#dropdown_data_type').val();

					if (type == 'notification')
						pageName = "ajax_load_notifications.php";
					else if (type == 'message')
						pageName = "ajax_load_messages.php";

					var ajaxReq = $.ajax({
						url: "includes/handlers/" + pageName,
						type: "POST",
						data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
						cache: false,

						success: function(response) {
							$('.dropdown-data-window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
							$('.dropdown-data-window').find('.noMoreDropdownData').remove();

							$('.dropdown-data-window').append(response);
						}
					});

				} //End if 

				return false;

			}); //End (window).scroll(function())


		});
	</script>