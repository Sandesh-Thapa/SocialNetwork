<?php
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");


if (isset($_POST['post'])) {
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'], 'none');
}

?>

</div>
<div class="post">
	<div class="profile-image">
		<a href="<?php echo $userLoggedIn; ?>">
			<img src="<?php echo $user['profile_pic']; ?>">
		</a>
	</div>
	<form action="index.php" class="form" method="POST">
		<textarea name="post_text" id="post_text" placeholder="What's on your mind, <?php echo $user['first_name']; ?>"></textarea>
		<input type="submit" name="post" id="post" value="Post">
	</form>
</div>


<div class="posts-section" id="load-data"></div>
<div id="loading" class="load-message">
	<img src='assets/images/icons/loading.gif'>
	<p>Loading Posts ....</p>
</div>





<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first posts 
		$.ajax({
			url: "includes/handlers/ajax_load_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn,
			cache: false,

			success: function(data) {
				$('#loading').hide();
				$('#load-data').html(data);
			}
		});

		$(window).scroll(function() {
			var height = $('#load-data').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var page = $('#load-data').find('.nextPage').val();
			var noMorePosts = $('#load-data').find('.noMorePosts').val();

			if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
				$('#loading').show();

				var ajaxReq = $.ajax({
					url: "includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache: false,

					success: function(response) {
						$('#load-data').find('.nextPage').remove(); //Removes current .nextpage 
						$('#load-data').find('.noMorePosts').remove();

						$('#loading').hide();
						$('#load-data').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});
</script>




</div>
</body>

</html>