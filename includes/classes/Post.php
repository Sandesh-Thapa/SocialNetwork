<?php
class Post
{
	private $user_obj;
	private $con;

	public function __construct($con, $user)
	{
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitPost($body, $user_to)
	{
		$body = strip_tags($body); //removes html tags 
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces 

		if ($check_empty != "") {

			$body_array = preg_split("/\s+/", $body);

			foreach ($body_array as $key => $value) {
				if (strpos($value, "www.youtube.com/watch?v=") !== false) {

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'500\' height=\'315\' src=\'" . $value . "\'></iframe><br>";
					$body_array[$key] = $value;
				}
			}
			$body = implode(" ", $body_array);

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			//If user is on own profile, user_to is 'none'
			if ($user_to == $added_by) {
				$user_to = "none";
			}

			//insert post 
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");
			$returned_id = mysqli_insert_id($this->con);

			//Insert notification 
			if ($user_to != 'none') {
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "profile_post");
			}

			//Update post count for user 
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
		}
	}

	public function loadPostsFriends($data, $limit)
	{
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if ($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

		if (mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				//Prepare user_to string so it can be included even if not posted to a user
				if ($row['user_to'] == "none") {
					$user_to = "";
				} else {
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "<i class='fas fa-caret-right'></i>&nbsp;<a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if ($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if ($user_logged_obj->isFriend($added_by)) {

					if ($num_iterations++ < $start)
						continue;


					//Once 10 posts have been loaded, break
					if ($count > $limit) {
						break;
					} else {
						$count++;
					}

					if ($userLoggedIn == $added_by)
						$delete_button = "<div class='delete-button'>
											<button id='delete-post$id' title='Delete Post' onClick='openModal$id()'>&times;</button>
										</div>";
					else
						$delete_button = "";

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
					$comments_check_num = mysqli_num_rows($comments_check);

					$like_check = mysqli_query($this->con, "SELECT * FROM likes WHERE post_id='$id'");
					$like_check_num = mysqli_num_rows($like_check);

					$liked_by_me = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
					$check_liked_by_me = mysqli_num_rows($liked_by_me);

					if ($check_liked_by_me > 0) {
						echo "<script type='text/Javascript'>
						 		likedPost();
						 	  </script>";
					}

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if ($interval->y >= 1) {
						if ($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					} else if ($interval->m >= 1) {
						if ($interval->d == 0) {
							$days = " ago";
						} else if ($interval->d == 1) {
							$days = $interval->d . " day ago";
						} else {
							$days = $interval->d . " days ago";
						}


						if ($interval->m == 1) {
							$time_message = $interval->m . " month" . $days;
						} else {
							$time_message = $interval->m . " months" . $days;
						}
					} else if ($interval->d >= 1) {
						if ($interval->d == 1) {
							$time_message = "Yesterday";
						} else {
							$time_message = $interval->d . " days ago";
						}
					} else if ($interval->h >= 1) {
						if ($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						} else {
							$time_message = $interval->h . " hours ago";
						}
					} else if ($interval->i >= 1) {
						if ($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						} else {
							$time_message = $interval->i . " minutes ago";
						}
					} else {
						if ($interval->s < 30) {
							$time_message = "Just now";
						} else {
							$time_message = $interval->s . " seconds ago";
						}
					}

					$userLoggedProfilePic = $this->user_obj->getProfilePic();

					$str .= "<div class='status-post'>
								<div class='title'>
									<div class='post-profile-pic'>
										<img src='$profile_pic' width='50'>
									</div>

									<div class='posted-by'>
										<a href='$added_by'> $first_name $last_name </a> &nbsp;$user_to
										<p>$time_message</p>
									</div>
									$delete_button
								</div>
								
								<div class='post-body'>
									$body
								</div>
								<div class='line'></div>
								<div class='like-comment'>
									<button class='btn' id='like-btn$id' onClick='likePost$id()'><i class='far fa-thumbs-up'></i> Like ($like_check_num)</button>
									<button class='btn' id='comment-btn$id()' onClick='javascript:toggle$id()'><i class='far fa-comment'></i> Comment ($comments_check_num)</button>
								</div>
							</div>
							<div id='toggleComment$id' class='toggle-comment'>
								<iframe src='comment_frame.php?post_id=$id' class='comment-iframe'></iframe>
							</div>
							<div class='comment-container'>
								<div class='loggedin-profile-pic'>
									<img src='$userLoggedProfilePic'>
								</div>
								<div class='comment-form'>
									<div class='form'>
										<textarea id='post_body$id' placeholder='Write a comment...' autocomplete='off'></textarea>
										<input type='hidden' id='post_id$id' value='$id'>
										<button name='submit' id='submit$id' onClick='postComment$id()'><i class='fas fa-paper-plane'></i></button>
									</div>
									<h3 class='success-message' id='success$id'></h3>
								</div>
							</div>

							<div id='modal$id' class='modal'>
								<div class='modal-content'>
									<div class='modal-header'>
										<h2>Delete Post</h2>
										<span class='close' onClick='closeModal$id()'>&times;</span>
									</div>
									<div class='modal-body'>
										<p>Are you sure you want to delete this post?</p>
										<div class='yes-no'>
											<button id='delete$id' onClick='deletePost$id()'><i class='far fa-trash-alt'></i> Yes, delete it</button>
											<button onClick='closeModal$id()'>Cancel</button>
										</div>
									</div>
								</div>
							</div>
						";
				}

?>
				<script>
					window.addEventListener('click', outsideClick);
					// Close If Outside Click
					function outsideClick(e) {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						if (e.target == modal) {
							modal.style.display = 'none';
						}
					}

					function openModal<?php echo $id; ?>() {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						modal.style.display = "block";
					}

					function closeModal<?php echo $id; ?>() {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						modal.style.display = 'none';
					}

					function likedPost() {
						var numLikes = <?php echo $like_check_num; ?>;
						var element = document.getElementById("like-btn<?php echo $id; ?>");
						element.style.color = "#dd123d";
						element.innerHTML = "<i class='far fa-thumbs-up'></i> Liked (" + numLikes + ")</button>";

					}

					function toggle<?php echo $id; ?>() {
						var element = document.getElementById("toggleComment<?php echo $id; ?>");

						if (element.style.display == "block")
							element.style.display = "none";
						else
							element.style.display = "block";
					}

					function deletePost<?php echo $id; ?>() {
						var postid = '<?php echo $id; ?>'
						var modal = document.getElementById("modal<?php echo $id; ?>");

						//alert('delete button ' + postid);
						var xhr = new XMLHttpRequest();
						xhr.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								modal.style.display = 'none';
								window.location.reload(true);
							}
						}
						xhr.open("GET", "includes/handlers/ajax_delete_posts.php?&postid=" + postid, true);
						xhr.send();
					}


					function likePost<?php echo $id; ?>() {
						var postid = <?php echo $id; ?>;
						var btn = document.getElementById("like-btn<?php echo $id; ?>");
						//alert("Post id: " + postid);	
						var xhr = new XMLHttpRequest();
						xhr.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								// btn.style.color = "red";
								// btn.innerHTML = "<i class='far fa-thumbs-up'></i> Liked";
								var statusMessage = this.responseText;
								var result = JSON.parse(statusMessage);
								//console.log(result);
								if (result[1] == "Liked") {
									btn.style.color = "#dd123d";
									btn.innerHTML = "<i class='far fa-thumbs-up'></i> Liked (" + result[0] + ")";
								} else //if (result[1] == "Like") 
								{
									btn.style.color = "#000";
									btn.innerHTML = "<i class='far fa-thumbs-up'></i> Like (" + result[0] + ")";
								}
							}
						}
						xhr.open("GET", "includes/handlers/ajax_like_posts.php?&postid=" + postid, true);
						xhr.send();


					}

					function postComment<?php echo $id; ?>() {
						var textbox = document.getElementById("post_body<?php echo $id; ?>");
						var postBody = document.getElementById("post_body<?php echo $id; ?>").value;
						var postid = document.getElementById("post_id<?php echo $id; ?>").value;
						var successMsg = document.getElementById("success<?php echo $id; ?>");

						if (postBody != "") {
							var xhr = new XMLHttpRequest();
							xhr.onreadystatechange = function() {
								if (this.readyState == 4 && this.status == 200) {
									successMsg.style.display = "block";
									successMsg.innerHTML = this.responseText;
									setTimeout(function() {
										successMsg.style.display = "none"
									}, 3000);
									textbox.value = '';
								}
							}
							xhr.open("GET", "includes/handlers/ajax_post_comments.php?postBody=" + postBody + "&postid=" + postid, true);
							xhr.send();
							postBody.value = "";
						} else {
							successMsg.innerHTML = "Type Comment First";
							successMsg.style.display = "block";
							setTimeout(function() {
								successMsg.style.display = "none"
							}, 3000);
						}


					}
				</script>

			<?php
			} //End while loop

			if ($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p class='noposts'> No more posts to show! </p>";
		}

		echo $str;
	}

	public function loadProfilePosts($data, $limit)
	{

		$page = $data['page'];
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if ($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to = '$profileUser') ORDER BY id DESC");

		if (mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				if ($num_iterations++ < $start)
					continue;


				//Once 10 posts have been loaded, break
				if ($count > $limit) {
					break;
				} else {
					$count++;
				}

				if ($userLoggedIn == $added_by)
					$delete_button = "<div class='delete-button'>
											<button id='delete-post$id' title='Delete Post' onClick='openModal$id()'>&times;</button>
										</div>";
				else
					$delete_button = "";

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
				$comments_check_num = mysqli_num_rows($comments_check);

				$like_check = mysqli_query($this->con, "SELECT * FROM likes WHERE post_id='$id'");
				$like_check_num = mysqli_num_rows($like_check);

				$liked_by_me = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
				$check_liked_by_me = mysqli_num_rows($liked_by_me);

				if ($check_liked_by_me > 0) {
					echo "<script type='text/Javascript'>
						 		likedPost();
						 	  </script>";
				}

				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date); //Difference between dates 
				if ($interval->y >= 1) {
					if ($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else
						$time_message = $interval->y . " years ago"; //1+ year ago
				} else if ($interval->m >= 1) {
					if ($interval->d == 0) {
						$days = " ago";
					} else if ($interval->d == 1) {
						$days = $interval->d . " day ago";
					} else {
						$days = $interval->d . " days ago";
					}


					if ($interval->m == 1) {
						$time_message = $interval->m . " month " . $days;
					} else {
						$time_message = $interval->m . " months " . $days;
					}
				} else if ($interval->d >= 1) {
					if ($interval->d == 1) {
						$time_message = "Yesterday";
					} else {
						$time_message = $interval->d . " days ago";
					}
				} else if ($interval->h >= 1) {
					if ($interval->h == 1) {
						$time_message = $interval->h . " hour ago";
					} else {
						$time_message = $interval->h . " hours ago";
					}
				} else if ($interval->i >= 1) {
					if ($interval->i == 1) {
						$time_message = $interval->i . " minute ago";
					} else {
						$time_message = $interval->i . " minutes ago";
					}
				} else {
					if ($interval->s < 30) {
						$time_message = "Just now";
					} else {
						$time_message = $interval->s . " seconds ago";
					}
				}

				$userLoggedProfilePic = $this->user_obj->getProfilePic();

				$str .= "<div class='status-post'>
								<div class='title'>
									<div class='post-profile-pic'>
										<img src='$profile_pic' width='50'>
									</div>

									<div class='posted-by'>
										<a href='$added_by'> $first_name $last_name </a>
										<p>$time_message</p>
									</div>
									$delete_button
								</div>
								
								<div class='post-body'>
									$body
								</div>
								<div class='line'></div>
								<div class='like-comment'>
									<button class='btn' id='like-btn$id' onClick='likePost$id()'><i class='far fa-thumbs-up'></i> Like ($like_check_num)</button>
									<button class='btn' id='comment-btn$id()' onClick='javascript:toggle$id()'><i class='far fa-comment'></i> Comment ($comments_check_num)</button>
								</div>
							</div>
							<div id='toggleComment$id' class='toggle-comment'>
								<iframe src='comment_frame.php?post_id=$id' class='comment-iframe'></iframe>
							</div>
							<div class='comment-container'>
								<div class='loggedin-profile-pic'>
									<img src='$userLoggedProfilePic'>
								</div>
								<div class='comment-form'>
									<div class='form'>
										<textarea id='post_body$id' placeholder='Write a comment...' autocomplete='off'></textarea>
										<input type='hidden' id='post_id$id' value='$id'>
										<button name='submit' id='submit$id' onClick='postComment$id()'><i class='fas fa-paper-plane'></i></button>
									</div>
									<h3 class='success-message' id='success$id'></h3>
								</div>
							</div>

							<div id='modal$id' class='modal'>
								<div class='modal-content'>
									<div class='modal-header'>
										<h2>Delete Post</h2>
										<span class='close' onClick='closeModal$id()'>&times;</span>
									</div>
									<div class='modal-body'>
										<p>Are you sure you want to delete this post?</p>
										<div class='yes-no'>
											<button id='delete$id' onClick='deletePost$id()'><i class='far fa-trash-alt'></i> Yes, delete it</button>
											<button onClick='closeModal$id()'>Cancel</button>
										</div>
									</div>
								</div>
							</div>
						";


			?>
				<script>
					window.addEventListener('click', outsideClick);
					// Close If Outside Click
					function outsideClick(e) {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						if (e.target == modal) {
							modal.style.display = 'none';
						}
					}

					function openModal<?php echo $id; ?>() {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						modal.style.display = "block";
					}

					function closeModal<?php echo $id; ?>() {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						modal.style.display = 'none';
					}

					function likedPost() {
						var numLikes = <?php echo $like_check_num; ?>;
						var element = document.getElementById("like-btn<?php echo $id; ?>");
						element.style.color = "#dd123d";
						element.innerHTML = "<i class='far fa-thumbs-up'></i> Liked (" + numLikes + ")</button>";

					}

					function toggle<?php echo $id; ?>() {
						var element = document.getElementById("toggleComment<?php echo $id; ?>");

						if (element.style.display == "block")
							element.style.display = "none";
						else
							element.style.display = "block";
					}

					function deletePost<?php echo $id; ?>() {
						var postid = '<?php echo $id; ?>'
						var modal = document.getElementById("modal<?php echo $id; ?>");

						//alert('delete button ' + postid);
						var xhr = new XMLHttpRequest();
						xhr.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								modal.style.display = 'none';
								window.location.reload(true);
							}
						}
						xhr.open("GET", "includes/handlers/ajax_delete_posts.php?&postid=" + postid, true);
						xhr.send();
					}


					function likePost<?php echo $id; ?>() {
						var postid = <?php echo $id; ?>;
						var btn = document.getElementById("like-btn<?php echo $id; ?>");
						//alert("Post id: " + postid);	
						var xhr = new XMLHttpRequest();
						xhr.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								// btn.style.color = "red";
								// btn.innerHTML = "<i class='far fa-thumbs-up'></i> Liked";
								var statusMessage = this.responseText;
								var result = JSON.parse(statusMessage);
								//console.log(result);
								if (result[1] == "Liked") {
									btn.style.color = "#dd123d";
									btn.innerHTML = "<i class='far fa-thumbs-up'></i> Liked (" + result[0] + ")";
								} else //if (result[1] == "Like") 
								{
									btn.style.color = "#000";
									btn.innerHTML = "<i class='far fa-thumbs-up'></i> Like (" + result[0] + ")";
								}
							}
						}
						xhr.open("GET", "includes/handlers/ajax_like_posts.php?&postid=" + postid, true);
						xhr.send();


					}

					function postComment<?php echo $id; ?>() {
						var textbox = document.getElementById("post_body<?php echo $id; ?>");
						var postBody = document.getElementById("post_body<?php echo $id; ?>").value;
						var postid = document.getElementById("post_id<?php echo $id; ?>").value;
						var successMsg = document.getElementById("success<?php echo $id; ?>");

						if (postBody != "") {
							var xhr = new XMLHttpRequest();
							xhr.onreadystatechange = function() {
								if (this.readyState == 4 && this.status == 200) {
									successMsg.style.display = "block";
									successMsg.innerHTML = this.responseText;
									setTimeout(function() {
										successMsg.style.display = "none"
									}, 3000);
									textbox.value = '';
								}
							}
							xhr.open("GET", "includes/handlers/ajax_post_comments.php?postBody=" + postBody + "&postid=" + postid, true);
							xhr.send();
							postBody.value = "";
						} else {
							successMsg.innerHTML = "Type Comment First";
							successMsg.style.display = "block";
							setTimeout(function() {
								successMsg.style.display = "none"
							}, 3000);
						}


					}
				</script>

			<?php
			} //End while loop

			if ($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p class='noposts'> No more posts to show! </p>";
		}

		echo $str;
	}

	public function getSinglePost($post_id)
	{
		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

		if (mysqli_num_rows($data_query) > 0) {

			$row = mysqli_fetch_array($data_query);
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];

			//Prepare user_to string so it can be included even if not posted to a user
			if ($row['user_to'] == "none") {
				$user_to = "";
			} else {
				$user_to_obj = new User($this->con, $row['user_to']);
				$user_to_name = $user_to_obj->getFirstAndLastName();
				$user_to = "<i class='fas fa-caret-right'></i>&nbsp;<a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
			}

			//Check if user who posted, has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if ($added_by_obj->isClosed()) {
				return;
			}

			$user_logged_obj = new User($this->con, $userLoggedIn);
			if ($user_logged_obj->isFriend($added_by)) {

				if ($userLoggedIn == $added_by)
					$delete_button = "<div class='delete-button'>
											<button id='delete-post$id' title='Delete Post' onClick='openModal$id()'>&times;</button>
										</div>";
				else
					$delete_button = "";

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
				$comments_check_num = mysqli_num_rows($comments_check);

				$like_check = mysqli_query($this->con, "SELECT * FROM likes WHERE post_id='$id'");
				$like_check_num = mysqli_num_rows($like_check);

				$liked_by_me = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
				$check_liked_by_me = mysqli_num_rows($liked_by_me);

				if ($check_liked_by_me > 0) {
					echo "<script type='text/Javascript'>
						 		likedPost();
						 	  </script>";
				}

				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date); //Difference between dates 
				if ($interval->y >= 1) {
					if ($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else
						$time_message = $interval->y . " years ago"; //1+ year ago
				} else if ($interval->m >= 1) {
					if ($interval->d == 0) {
						$days = " ago";
					} else if ($interval->d == 1) {
						$days = $interval->d . " day ago";
					} else {
						$days = $interval->d . " days ago";
					}


					if ($interval->m == 1) {
						$time_message = $interval->m . " month" . $days;
					} else {
						$time_message = $interval->m . " months" . $days;
					}
				} else if ($interval->d >= 1) {
					if ($interval->d == 1) {
						$time_message = "Yesterday";
					} else {
						$time_message = $interval->d . " days ago";
					}
				} else if ($interval->h >= 1) {
					if ($interval->h == 1) {
						$time_message = $interval->h . " hour ago";
					} else {
						$time_message = $interval->h . " hours ago";
					}
				} else if ($interval->i >= 1) {
					if ($interval->i == 1) {
						$time_message = $interval->i . " minute ago";
					} else {
						$time_message = $interval->i . " minutes ago";
					}
				} else {
					if ($interval->s < 30) {
						$time_message = "Just now";
					} else {
						$time_message = $interval->s . " seconds ago";
					}
				}

				$userLoggedProfilePic = $this->user_obj->getProfilePic();

				$str .= "<div class='status-post'>
								<div class='title'>
									<div class='post-profile-pic'>
										<img src='$profile_pic' width='50'>
									</div>

									<div class='posted-by'>
										<a href='$added_by'> $first_name $last_name </a> &nbsp;$user_to
										<p>$time_message</p>
									</div>
									$delete_button
								</div>
								
								<div class='post-body'>
									$body
								</div>
								<div class='line'></div>
								<div class='like-comment'>
									<button class='btn' id='like-btn$id' onClick='likePost$id()'><i class='far fa-thumbs-up'></i> Like ($like_check_num)</button>
									<button class='btn' id='comment-btn$id()' onClick='javascript:toggle$id()'><i class='far fa-comment'></i> Comment ($comments_check_num)</button>
								</div>
							</div>
							<div id='toggleComment$id' class='toggle-comment'>
								<iframe src='comment_frame.php?post_id=$id' class='comment-iframe'></iframe>
							</div>
							<div class='comment-container'>
								<div class='loggedin-profile-pic'>
									<img src='$userLoggedProfilePic'>
								</div>
								<div class='comment-form'>
									<div class='form'>
										<textarea id='post_body$id' placeholder='Write a comment...' autocomplete='off'></textarea>
										<input type='hidden' id='post_id$id' value='$id'>
										<button name='submit' id='submit$id' onClick='postComment$id()'><i class='fas fa-paper-plane'></i></button>
									</div>
									<h3 class='success-message' id='success$id'></h3>
								</div>
							</div>

							<div id='modal$id' class='modal'>
								<div class='modal-content'>
									<div class='modal-header'>
										<h2>Delete Post</h2>
										<span class='close' onClick='closeModal$id()'>&times;</span>
									</div>
									<div class='modal-body'>
										<p>Are you sure you want to delete this post?</p>
										<div class='yes-no'>
											<button id='delete$id' onClick='deletePost$id()'><i class='far fa-trash-alt'></i> Yes, delete it</button>
											<button onClick='closeModal$id()'>Cancel</button>
										</div>
									</div>
								</div>
							</div>
						";


			?>
				<script>
					window.addEventListener('click', outsideClick);
					// Close If Outside Click
					function outsideClick(e) {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						if (e.target == modal) {
							modal.style.display = 'none';
						}
					}

					function openModal<?php echo $id; ?>() {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						modal.style.display = "block";
					}

					function closeModal<?php echo $id; ?>() {
						var modal = document.getElementById("modal<?php echo $id; ?>");
						modal.style.display = 'none';
					}

					function likedPost() {
						var numLikes = <?php echo $like_check_num; ?>;
						var element = document.getElementById("like-btn<?php echo $id; ?>");
						element.style.color = "#dd123d";
						element.innerHTML = "<i class='far fa-thumbs-up'></i> Liked (" + numLikes + ")</button>";

					}

					function toggle<?php echo $id; ?>() {
						var element = document.getElementById("toggleComment<?php echo $id; ?>");

						if (element.style.display == "block")
							element.style.display = "none";
						else
							element.style.display = "block";
					}

					function deletePost<?php echo $id; ?>() {
						var postid = '<?php echo $id; ?>'
						var modal = document.getElementById("modal<?php echo $id; ?>");

						//alert('delete button ' + postid);
						var xhr = new XMLHttpRequest();
						xhr.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								modal.style.display = 'none';
								window.location.reload(true);
							}
						}
						xhr.open("GET", "includes/handlers/ajax_delete_posts.php?&postid=" + postid, true);
						xhr.send();
					}


					function likePost<?php echo $id; ?>() {
						var postid = <?php echo $id; ?>;
						var btn = document.getElementById("like-btn<?php echo $id; ?>");
						//alert("Post id: " + postid);	
						var xhr = new XMLHttpRequest();
						xhr.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								// btn.style.color = "red";
								// btn.innerHTML = "<i class='far fa-thumbs-up'></i> Liked";
								var statusMessage = this.responseText;
								var result = JSON.parse(statusMessage);
								//console.log(result);
								if (result[1] == "Liked") {
									btn.style.color = "#dd123d";
									btn.innerHTML = "<i class='far fa-thumbs-up'></i> Liked (" + result[0] + ")";
								} else //if (result[1] == "Like") 
								{
									btn.style.color = "#000";
									btn.innerHTML = "<i class='far fa-thumbs-up'></i> Like (" + result[0] + ")";
								}
							}
						}
						xhr.open("GET", "includes/handlers/ajax_like_posts.php?&postid=" + postid, true);
						xhr.send();


					}

					function postComment<?php echo $id; ?>() {
						var textbox = document.getElementById("post_body<?php echo $id; ?>");
						var postBody = document.getElementById("post_body<?php echo $id; ?>").value;
						var postid = document.getElementById("post_id<?php echo $id; ?>").value;
						var successMsg = document.getElementById("success<?php echo $id; ?>");

						if (postBody != "") {
							var xhr = new XMLHttpRequest();
							xhr.onreadystatechange = function() {
								if (this.readyState == 4 && this.status == 200) {
									successMsg.style.display = "block";
									successMsg.innerHTML = this.responseText;
									setTimeout(function() {
										successMsg.style.display = "none"
									}, 3000);
									textbox.value = '';
								}
							}
							xhr.open("GET", "includes/handlers/ajax_post_comments.php?postBody=" + postBody + "&postid=" + postid, true);
							xhr.send();
							postBody.value = "";
						} else {
							successMsg.innerHTML = "Type Comment First";
							successMsg.style.display = "block";
							setTimeout(function() {
								successMsg.style.display = "none"
							}, 3000);
						}


					}
				</script>

<?php
			} else {
				echo "<p>You cannot see this post because you are not friend with this user!</p>";
				return;
			}
		} else {
			echo "<p>No post found. May be user had deleted the post!</p>";
			return;
		}

		echo $str;
	}
}
