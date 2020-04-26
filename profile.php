<?php
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");
require 'includes/form_handlers/profile_post.php';

// if (isset($_POST['post'])) {
//     $post = new Post($con, $userLoggedIn);
//     $post->submitPost($_POST['post_text'], $_POST['user_to']);
// }

if (isset($_GET['profile_username'])) {
    $username = $_GET['profile_username'];
}

$user_obj = new User($con, $username);
$logged_in_user_obj = new User($con, $userLoggedIn);

if (isset($_POST['remove_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);
}

if (isset($_POST['send_request'])) {
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);
}

if (isset($_POST['cancel_request'])) {
    $user = new User($con, $userLoggedIn);
    $user->cancelRequest($username);
}

if (isset($_POST['respond_request'])) {
    header("Location: request.php");
}



?>


<div class="showcase">
    <div class="cover-pic">
        <img src="<?php echo $user_obj->getCoverPic(); ?>">
    </div>
    <div class="profile-pic">
        <img src="<?php echo $user_obj->getProfilePic(); ?>">
        <a href="<?php echo $username; ?>"><?php echo $user_obj->getFirstAndLastName(); ?></a>
    </div>
    <div class="add-remove">
        <form action="<?php echo $username; ?>" method="POST">
            <?php
            if ($user_obj->isClosed()) {
                header("Location: user_closed.php");
            }

            if ($userLoggedIn != $username) {
                if ($logged_in_user_obj->isFriend($username)) {
                    echo '<button type="submit" name="remove_friend" class="btn remove-friend"><i class="fas fa-user-minus"></i> Unfriend</button>';
                } elseif ($logged_in_user_obj->didReceiveRequest($username)) {
                    echo '<button type="submit" name="respond_request" class="btn confirm-friend"><i class="fas fa-user-edit"></i> Respond to Friend Request</button>';
                } elseif ($user_obj->didReceiveRequest($userLoggedIn)) {
                    echo '<button type="submit" name="cancel_request" class="btn remove-request"><i class="fas fa-user-times"></i> Cancel Request</button>';
                } else
                    echo '<button type="submit" name="send_request" class="btn send-request"><i class="fas fa-user-plus"></i> Add Friend</button>';
            }
            ?>
        </form>
    </div>
</div>

<div class="profile-wrapper">
    <div class="user-info">
        <p class="num-posts"><i class="fas fa-newspaper"></i>&nbsp;&nbsp;&nbsp;Posts: <?php echo $user_obj->getNumPosts(); ?></p>
        <p class="num-posts"><i class="fas fa-thumbs-up"></i>&nbsp;&nbsp;&nbsp;Likes: <?php echo $user_obj->getNumLikes(); ?></p>
        <p class="num-posts"><i class="fas fa-users"></i>&nbsp;&nbsp;&nbsp;Friends: <?php echo $user_obj->getNumFriends(); ?></p>
        <?php if ($userLoggedIn != $username) {
            echo "<p class='num-posts'><i class='fas fa-user-friends'></i>&nbsp;&nbsp;&nbsp;Mutual Friends:" . $logged_in_user_obj->getMutualFriends($username) . " </p>";
        }
        ?>
    </div>
    <div class="profile-posts">
        <div class="post-from-profile">
            <div class="loggedIn-img">
                <a href="<?php echo $userLoggedIn; ?>">
                    <img src="<?php echo $logged_in_user_obj->getProfilePic(); ?>">
                </a>
            </div>
            <form action="<?php echo $username; ?>" method="POST">
                <textarea name="post_text" id="post_text" placeholder="Write something to <?php echo $user_obj->getFirstAndLastName(); ?>"></textarea>
                <input type="hidden" name="user_to" value="<?php echo $user_obj->getUsername(); ?>">
                <input type="submit" name="post" id="post" value="Post">
            </form>
        </div>
        <div class="posts-section" id="load-data"></div>
        <div id="loading" class="load-message">
            <img src='assets/images/icons/loading.gif'>
            <p>Loading Posts ....</p>
        </div>
    </div>
</div>

<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username; ?>';

    $(document).ready(function() {

        $('#loading').show();

        //Original ajax request for loading first posts 
        $.ajax({
            url: "includes/handlers/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
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
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
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