<?php
require 'config/config.php';
include("classes/User.php");
include("classes/Post.php");
include("classes/Message.php");
include("classes/Notification.php");


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
					<button id="searchLive"><i class="fas fa-search"></i></button>
				</form>
			</div>
			<div class="tools">

				<?php
				//unread messages
				$messages = new Message($con, $userLoggedIn);
				$num_messages = $messages->getUnreadNumber();

				//unread notifications
				$notifications = new Notification($con, $userLoggedIn);
				$num_notifications = $notifications->getUnreadNumber();

				//unread notifications
				$user_obj = new User($con, $userLoggedIn);
				$num_requests = $user_obj->getNumberOfFriendRequests();
				?>

				<a href="<?php echo $userLoggedIn; ?>" class="name" title="Profile">
					<img src="<?php echo $user['profile_pic']; ?>">
					<?php echo $user['first_name']; ?>
				</a>
				<a href="index.php" title="Home">
					<i class="fas fa-home"></i>
				</a>
				<a href="request.php" title="Friend Request">
					<i class="fas fa-user-friends"></i>
					<?php
					if ($num_requests > 0)
						echo '<span class="notification-badge" id="unread_requests">' . $num_requests . '</span>';
					?>
				</a>
				<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')" title="Messsage">
					<i class="fas fa-envelope"></i>
					<?php
					if ($num_messages > 0)
						echo '<span class="notification-badge" id="unread_message">' . $num_messages . '</span>';
					?>
				</a>
				<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')" title="Notification">
					<i class="fas fa-bell"></i>
					<?php
					if ($num_notifications > 0)
						echo '<span class="notification-badge" id="unread_notification">' . $num_notifications . '</span>';
					?>
				</a>
				<a href="settings.php" title="Settings">
					<i class="fas fa-cog"></i>
				</a>
				<a href="includes/handlers/logout.php" title="Log out">
					<i class="fas fa-sign-out-alt"></i>
				</a>
			</div>
		</div>
	</header>
	<div class="search_results"></div>
	<div class="search_results_footer_empty"></div>
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