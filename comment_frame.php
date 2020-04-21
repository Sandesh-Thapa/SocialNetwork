<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: register.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="assets/css/all.min.css">
    <style>
        body {
            background: #fff;
            overflow-x: hidden;
        }
    </style>
</head>

<body>


    <?php

    //get id of post
    if (isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
    }

    $get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER by id ASC");
    $count = mysqli_num_rows($get_comments);

    if ($count != 0) {
        while ($comment = mysqli_fetch_array($get_comments)) {
            $comment_body = $comment['post_body'];
            $posted_to = $comment['posted_to'];
            $posted_by = $comment['posted_by'];
            $date_added = $comment['date_added'];
            $removed = $comment['removed'];

            //Timeframe
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($date_added); //Time of post
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

            $user_obj = new User($con, $posted_by);
            // $profile_pic_query = mysqli_query($con, "SELECT profile_pic FROM users WHERE username = '$posted_by'");
            // $profile_pic = mysqli_fetch_array($profile_pic_query);

    ?>
            <div class="display-comment">
                <div class="comment-wrapper">
                    <a class="comment-profile-pic" href="<?php echo $posted_by ?>" target="_parent">
                        <img src="<?php echo $user_obj->getProfilePic(); ?>">
                    </a>
                </div>
                <p class="comment-body">
                    <a href="<?php echo $posted_by; ?>"><?php echo $user_obj->getFirstAndLastName(); ?></a>
                    &nbsp;&nbsp;
                    <?php echo $comment_body; ?>
                </p>
            </div>
            <p class="time-message"> <?php echo $time_message; ?></p>
    <?php
        }
    }

    ?>





</body>

</html>