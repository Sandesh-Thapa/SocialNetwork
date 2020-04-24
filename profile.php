<?php
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");

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


</body>

</html>