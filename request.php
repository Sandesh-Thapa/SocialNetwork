<?php
include("includes/header.php");
include("includes/classes/User.php");
?>
<div class="friend-requests">
    <div class="request-container">
        <?php
        $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
        $num_requests = mysqli_num_rows($query);
        if ($num_requests == 0)
            echo '<h4 class="no-requests">You have no friend request at this time!</h4>';
        else {
            echo "<div class='friend-request-header'>
                    <h4 class='requests'>Respond to Your $num_requests Friend Requests<h4>
                </div>";
            while ($row = mysqli_fetch_array($query)) {
                $user_from = $row['user_from'];
                $user_from_obj = new User($con, $user_from);

                $profile_pic = $user_from_obj->getProfilePic();
                $full_name = $user_from_obj->getFirstAndLastName();
                $user_from_friend_array = $user_from_obj->getFriendArray();

                if (isset($_POST['accept_request' . $user_from])) {
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$user_from,') WHERE username = '$userLoggedIn'");
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$userLoggedIn,') WHERE username = '$user_from'");

                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                    header("Location: request.php");
                }

                if (isset($_POST['ignore_request' . $user_from])) {
                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                    header("Location: request.php");
                }
                echo "<div class='all-friend-requests'>
                        <div class='requested-img'>
                            <img src='$profile_pic'>
                        </div>
                        <div class='requested-name'>
                            <h5><a href='$user_from'>$full_name</a></h5>
                        </div>
                        <form action='request.php' method='POST'>
                            <input type='submit' name='accept_request$user_from' class='accept-btn' value='Confirm'>
                            <input type='submit' name='ignore_request$user_from' class='ignore-btn' value='Delete Request'>
                        </form>
                      </div>
                ";
            }
        }

        ?>

    </div>
</div>