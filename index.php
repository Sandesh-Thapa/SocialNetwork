<?php
include("includes/header.php");


if (isset($_POST['post'])) {

	$uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";

	if ($imageName != "") {
		$targetDir = "assets/images/posts";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if ($_FILES['fileToUpload']['size'] > 10000000) {
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if (strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			echo "<script>
					alert('" . $errorMessage . "');
			</script>";
			$uploadOk = 0;
		}

		if ($uploadOk) {
			if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
				//image uploaded okay
			} else {
				//image did not upload
				$uploadOk = 0;
			}
		}
	}

	if ($uploadOk) {
		$post = new Post($con, $userLoggedIn);
		$post->submitPost($_POST['post_text'], 'none', $imageName);
	} else {
		echo "<script>
					alert('" . $errorMessage . "');
			</script>";
	}
}



?>
<div class="post">
	<div class="profile-image">
		<a href="<?php echo $userLoggedIn; ?>">
			<img src="<?php echo $user['profile_pic']; ?>">
		</a>
	</div>
	<form action="index.php" class="form" method="POST" enctype="multipart/form-data">
		<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
		<textarea name="post_text" id="post_text" placeholder="What's on your mind, <?php echo $user['first_name']; ?>"></textarea>
		<input type="submit" name="post" id="post" value="Post">
	</form>
	<button id="files"><i class="far fa-image"></i> Add Photo</button>
</div>


<div class="posts-section" id="load-data"></div>
<div id="loading" class="load-message">
	<img src='assets/images/icons/loading.gif'>
	<p>Loading Posts ....</p>
</div>


<script>
	var uploadBtn = document.getElementById("files");
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	uploadBtn.addEventListener('click', () => {
		document.getElementById("fileToUpload").click();
		return false;
	});


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





</body>

</html>